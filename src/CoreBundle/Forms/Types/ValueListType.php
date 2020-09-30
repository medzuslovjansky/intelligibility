<?php

declare(strict_types=1);

namespace Intelligibility\CoreBundle\Forms\Types;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValueListType extends AbstractType
{
    protected ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function getParent()
    {
        return TextareaType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->resetViewTransformers();

        $builder->addViewTransformer($this->createViewTransformer($options));
        $builder->addEventListener(FormEvents::POST_SUBMIT, $this->createValidationListener($options));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'separatorCharacter' => ',',
            'separatorCharacterList' => [',', ';', ':'], //all this values will be replaced to value in 'separatorCharacter'
            'max' => null,
            'valueValidator' => null,
        ]);
        $resolver->addAllowedTypes('max', ['int', 'NULL']);
    }

    /**
     * @return DataTransformerInterface
     */
    protected function createViewTransformer(array $options): CallbackTransformer
    {
        return new CallbackTransformer(
            function ($value) use ($options) {
                $separator = $options['separatorCharacter'];

                return implode($separator, (array) $value);
            },
            function ($value) use ($options) {
                $separator = $options['separatorCharacter'];
                $value = str_replace($options['separatorCharacterList'], $separator, $value);
                $values = explode($separator, $value);
                if (empty($values)) {
                    return;
                }
                $values = array_map(function ($v) {
                    return trim($v, ", \t\n\r\0\x0B");
                }, $values);

                return array_diff(array_unique($values), ['', '0', null]);
            }
        );
    }

    /**
     * @return callable
     */
    protected function createValidationListener(array $options)
    {
        return function (FormEvent $event) use ($options): void {
            $form = $event->getForm();
            $data = $form->getData();
            if (empty($data)) {
                return;
            }
            $validation = $options['valueValidator'];
            if (!$validation instanceof Constraint) {
                return;
            }

            foreach ($data as $value) {
                $result = $this->validator->validate($value, $validation);
                if ($result->count() > 0) {
                    $form->addError(new FormError($result->get(0)->getMessage().$value));
                }
            }
        };
    }
}
