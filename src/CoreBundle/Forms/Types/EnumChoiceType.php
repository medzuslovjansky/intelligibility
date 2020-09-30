<?php

declare(strict_types=1);

namespace Intelligibility\CoreBundle\Forms\Types;

use Intelligibility\CoreBundle\Enum\AbstractEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class EnumChoiceType extends AbstractType
{
    protected TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix()
    {
        return 'enum_choice';
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $choices = function (Options $options) {
            $values = AbstractEnum::getKeyAndTranslation($options['enumClass']);
            $choices = [];
            foreach ($values as $transValue) {
                $choices[$this->translator->trans($transValue['trans'])] = $transValue['value'];
            }
            if (null !== $options['choice_filter']) {
                $choices = $options['choice_filter']($choices);
            }

            return $choices;
        };

        $resolver->setDefaults([
            'enumClass' => null,
            'choices' => $choices,
            'constraint_min' => null,
            'constraint_max' => null,
            'choice_filter' => null,
            'choice_translation_domain' => false,
        ]);

        $resolver->setAllowedValues('enumClass', function ($enumClass) {
            if (!is_subclass_of($enumClass, AbstractEnum::class)) {
                throw new \InvalidOptionsException(sprintf('Invalid enumClass[%s]', $enumClass));
            }

            return true;
        });
        $resolver->setRequired('enumClass');
    }
}
