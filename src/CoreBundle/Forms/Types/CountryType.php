<?php

declare(strict_types=1);

namespace Intelligibility\CoreBundle\Forms\Types;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\IntlCallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Intl\Countries;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CountryType extends AbstractType
{
    protected const countriesBlackList = [
        'AF', 'IQ', 'IR', 'SO', 'YE', 'KP', 'AZ', 'AO', 'BD', 'BJ', 'BF', 'BI', 'BT', 'VU', 'HT', 'GM', 'GN', 'GW', 'CG', 'DJ', 'ZM', 'KH',
        'KE', 'KI', 'KM', 'CD', 'LA', 'LS', 'LR', 'MR', 'MG', 'MW', 'ML', 'MZ', 'MM', 'NP', 'NR', 'NE', 'NG', 'RW', 'ST', 'SN', 'SB', 'SD',
        'SL', 'TZ', 'TL', 'TG', 'TV', 'UG', 'CF', 'TD', 'ER', 'ET', 'SS', 'AI', 'AQ', 'AG', 'AR', 'AM', 'BB', 'BH', 'BQ', 'IO', 'BN', 'GA',
        'GY', 'GH', 'GG', 'GU', 'ZW', 'CV', 'QA', 'KG', 'CC', 'CR', 'CI', 'CU', 'MO', 'MY', 'MV', 'MX', 'NA', 'NI', 'NU', 'PK', 'PS', 'PG',
        'WS', 'SY', 'SR', 'TJ', 'TO', 'WF', 'ZA', 'JM',
    ];

    /**
     * {@inheritdoc}AF
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choice_loader' => function (Options $options) {
                $choiceTranslationLocale = $options['choice_translation_locale'];
                $alpha3 = $options['alpha3'];

                return new IntlCallbackChoiceLoader(function () use ($choiceTranslationLocale, $alpha3) {
                    $result = $alpha3 ? Countries::getAlpha3Names($choiceTranslationLocale) : Countries::getNames($choiceTranslationLocale);

                    foreach (self::countriesBlackList as $black) {
                        unset($result[$black]);
                    }

                    return array_flip($result);
                });
            },
            'choice_translation_domain' => false,
            'choice_translation_locale' => null,
            'alpha3' => false,
        ]);

        $resolver->setAllowedTypes('choice_translation_locale', ['null', 'string']);
        $resolver->setAllowedTypes('alpha3', 'bool');
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'country';
    }
}
