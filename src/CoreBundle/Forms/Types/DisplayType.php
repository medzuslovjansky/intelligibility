<?php

declare(strict_types=1);

namespace Intelligibility\CoreBundle\Forms\Types;

use Intelligibility\CoreBundle\Entity\MetaInterface;
use Intelligibility\CoreBundle\Util\Helper\IntelligibilityDateTimeHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class DisplayType extends AbstractType
{
    protected IntelligibilityDateTimeHelper $dateTimeFormatter;

    protected TranslatorInterface $translator;

    public function __construct(IntelligibilityDateTimeHelper $dateTimeFormatter, TranslatorInterface $translator)
    {
        $this->dateTimeFormatter = $dateTimeFormatter;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setDisabled(true);

        $builder->addViewTransformer(new CallbackTransformer(
            $this->getDisplayTransformerClosure($options), $this->getDisplayReverseTransformerClosure()
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);
        $view->vars['attr']['class'] = !empty($view->vars['attr']['class']) ? $view->vars['attr']['class'] .= ' ' : '';

        $view->vars['attr']['data-value'] = $this->normalizeOriginalValue($form->getNormData(), $options);
    }

    public function getParent()
    {
        return FormType::class;
    }

    public function getBlockPrefix()
    {
        return 'display';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'compound' => false,
            'read_only' => true,
            'disabled' => true,
            'required' => false,

            'dateFormatAlias' => 'DateShortTimeShort',
            'arraySeparator' => ', ',
        ]);

        $resolver->setAllowedValues('dateFormatAlias', [
            'DateShortTimeShort',   // 2017.04.05. 15:26
            'DateFullTimeShort',    // 2017.april.5. 15:26
            'Date',                 // 2017 apr.5
            'DateTime',             // 2017.apr.5 15:26:45
            'Time',                 // 15:26:45
            'DateShort',            // 2017.04.05.
            'DateLong',             // 2017.april.5.
        ]);
    }

    /**
     * @return \Closure
     */
    protected function getDisplayTransformerClosure(array $options)
    {
        $dateTimeFormatter = $this->dateTimeFormatter;

        return function ($data) use ($options, $dateTimeFormatter) {
            if ($data instanceof \DateTime) {
                return $dateTimeFormatter->formatByAlias($options['dateFormatAlias'], $data);
            } elseif (\is_object($data) && method_exists($data, '__toString')) {
                return (string) $data;
            } elseif (\is_object($data)) {
                throw new TransformationFailedException(sprintf('Object(%s) can not be transformed!', \get_class($data)));
            } elseif (\is_array($data)) {
                return implode($options['arraySeparator'], $data);
            } elseif (\is_bool($data)) {
                return $this->translator->trans($data ? 'VirgoStatic@button.yes' : 'VirgoStatic@button.no');
            } elseif (null === $data) {
                return 'Data null';
            }

            return (string) $data;
        };
    }

    protected function getDisplayReverseTransformerClosure()
    {
        return function ($data): void {};
    }

    private function normalizeOriginalValue($data, array $options)
    {
        $normalize = function ($data) use ($options, &$normalize) {
            if ($data instanceof \DateTime) {
                return $this->dateTimeFormatter->formatByAlias($options['dateFormatAlias'], $data);
            } elseif ($data instanceof MetaInterface) {
                return $data->getId();
            } elseif (\is_array($data)) {
                $newData = [];
                foreach ($data as $key => $val) {
                    $newData[$key] = $normalize($val);
                }

                return $newData;
            }

            return (string) $data;
        };

        if (\is_array($data)) {
            return json_encode($normalize($data));
        }

        return $normalize($data);
    }
}
