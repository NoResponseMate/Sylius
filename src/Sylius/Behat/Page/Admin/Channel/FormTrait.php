<?php

declare(strict_types=1);

namespace Sylius\Behat\Page\Admin\Channel;

trait FormTrait
{
    public function getDefinedFormElements(): array
    {
        return [
            'base_currency' => '#sylius_channel_baseCurrency',
            'code' => '#sylius_channel_code',
            'color' => '#sylius_channel_color',
            'contact_email' => '#sylius_channel_contactEmail',
            'contact_phone_number' => '#sylius_channel_contactPhoneNumber',
            'countries' => '#sylius_channel_countries',
            'currencies' => '#sylius_channel_currencies',
            'default_locale' => '#sylius_channel_defaultLocale',
            'default_tax_zone' => '#sylius_channel_defaultTaxZone',
            'discounted_products_checking_period' => '#sylius_channel_channelPriceHistoryConfig_lowestPriceForDiscountedProductsCheckingPeriod',
            'enabled' => '#sylius_channel_enabled',
            'hostname' => '#sylius_channel_hostname',
            'locales' => '#sylius_channel_locales',
            'menu_taxon' => '#sylius_channel_menuTaxon',
            'name' => '#sylius_channel_name',
            'tax_calculation_strategy' => '#sylius_channel_taxCalculationStrategy',
            'theme' => '#sylius_channel_themeName'
        ];
    }

    public function setHostname(string $hostname): void
    {
        $this->getElement('hostname')->setValue($hostname);
    }

    public function setTheme(string $themeName): void
    {
        $this->getElement('theme')->selectOption($themeName);
    }

    public function getTheme(): string
    {
        return $this->getElement('theme')->getValue();
    }

    public function setContactEmail(string $contactEmail): void
    {
        $this->getElement('contact_email')->setValue($contactEmail);
    }

    public function setContactPhoneNumber(string $contactPhoneNumber): void
    {
        $this->getElement('contact_phone_number')->setValue($contactPhoneNumber);
    }

    public function defineColor(string $color): void
    {
        $this->getElement('color')->setValue($color);
    }

    public function chooseCurrency(string $currencyName): void
    {
        $this->getElement('currencies')->selectOption($currencyName, true);
    }

    public function chooseLocale(string $language): void
    {
        $this->getElement('locales')->selectOption($language);
    }

    public function chooseDefaultTaxZone(string $taxZone): void
    {
        $this->getElement('default_tax_zone')->selectOption($taxZone);
    }

    public function chooseDefaultLocale(string $locale): void
    {
        $this->getElement('default_locale')->selectOption($locale);
    }

    public function chooseOperatingCountries(array $countries): void
    {
        foreach ($countries as $country) {
            $this->getElement('countries')->selectOption($country, true);
        }
    }

    public function chooseBaseCurrency(string $currency): void
    {
        $this->getElement('currencies')->selectOption($currency, true);
        $this->getElement('base_currency')->selectOption($currency);
    }

    public function getMenuTaxon(): string
    {
        return $this->getSelectedOptionText('menu_taxon');
    }

    public function specifyMenuTaxon(string $menuTaxon): void
    {
        $this->getElement('menu_taxon')->selectOption($menuTaxon);
    }

    public function getTaxCalculationStrategy(): string
    {
        return $this->getSelectedOptionText('tax_calculation_strategy');
    }

    public function chooseTaxCalculationStrategy(string $taxCalculationStrategy): void
    {
        $this->getElement('tax_calculation_strategy')->selectOption($taxCalculationStrategy);
    }

    private function getSelectedOptionText(string $element): string
    {
        return $this
            ->getElement($element)
            ->find('css', 'option:selected')
            ->getText()
        ;
    }
}
