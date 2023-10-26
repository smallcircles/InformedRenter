<?php

declare(strict_types=1);

namespace org\wplake\acf_views;

use DateTime;

defined('ABSPATH') || exit;

class Settings
{
    private Options $options;

    private string $version;
    private string $license;
    private string $licenseExpiration;
    private array $demoImport;
    private string $licenseUsedDomains;
    private string $licenseUsedDevDomains;
    private bool $isDevMode;
    private bool $isNotCollapsedFieldsByDefault;
    private bool $isWithoutFieldsCollapseCursor;

    public function __construct(Options $options)
    {
        $this->options = $options;

        $this->version = '';
        $this->license = '';
        $this->licenseExpiration = '';
        $this->licenseUsedDomains = '';
        $this->licenseUsedDevDomains = '';
        $this->demoImport = [];
        $this->isDevMode = false;
        $this->isNotCollapsedFieldsByDefault = false;
        $this->isWithoutFieldsCollapseCursor = false;
    }

    public function load(): void
    {
        $settings = (array)($this->options->getOption(Options::OPTION_SETTINGS) ?: []);

        if (isset($settings['version'])) {
            $this->version = (string)$settings['version'];
        }

        if (isset($settings['license'])) {
            $this->license = (string)$settings['license'];
        }

        if (isset($settings['licenseExpiration'])) {
            $this->licenseExpiration = (string)$settings['licenseExpiration'];
        }

        if (isset($settings['licenseUsedDomains'])) {
            $this->licenseUsedDomains = (string)$settings['licenseUsedDomains'];
        }

        if (isset($settings['licenseUsedDevDomains'])) {
            $this->licenseUsedDevDomains = (string)$settings['licenseUsedDevDomains'];
        }

        if (isset($settings['demoImport'])) {
            $this->demoImport = (array)$settings['demoImport'];
        }

        if (isset($settings['isDevMode'])) {
            $this->isDevMode = (bool)$settings['isDevMode'];
        }

        if (isset($settings['isNotCollapsedFieldsByDefault'])) {
            $this->isNotCollapsedFieldsByDefault = (bool)$settings['isNotCollapsedFieldsByDefault'];
        }

        if (isset($settings['isWithoutFieldsCollapseCursor'])) {
            $this->isWithoutFieldsCollapseCursor = (bool)$settings['isWithoutFieldsCollapseCursor'];
        }
    }

    public function save(): void
    {
        $settings = [
            'version' => $this->version,
            'license' => $this->license,
            'licenseExpiration' => $this->licenseExpiration,
            'licenseUsedDomains' => $this->licenseUsedDomains,
            'licenseUsedDevDomains' => $this->licenseUsedDevDomains,
            'demoImport' => $this->demoImport,
            'isDevMode' => $this->isDevMode,
            'isNotCollapsedFieldsByDefault' => $this->isNotCollapsedFieldsByDefault,
            'isWithoutFieldsCollapseCursor' => $this->isWithoutFieldsCollapseCursor,
        ];

        $this->options->updateOption(Options::OPTION_SETTINGS, $settings);
    }

    //// setters / getters

    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setLicense(string $license): void
    {
        $this->license = $license;
    }

    public function getLicense(): string
    {
        return $this->license;
    }

    public function isLicenseExpired(): bool
    {
        if (!$this->licenseExpiration) {
            return false;
        }

        $expirationDate = DateTime::createFromFormat('Ymd', $this->licenseExpiration);

        if (false === $expirationDate) {
            return false;
        }

        return $expirationDate < new DateTime();
    }

    public function isLicenseExpiresWithinMonth(): bool
    {
        if (!$this->licenseExpiration) {
            return false;
        }

        $expirationDate = DateTime::createFromFormat('Ymd', $this->licenseExpiration);

        if (false === $expirationDate) {
            return false;
        }

        $month = new DateTime();
        $month->modify('+1 month');

        return $expirationDate < $month;
    }

    public function isActiveLicense(): bool
    {
        return !!$this->license &&
            !!$this->licenseExpiration &&
            !$this->isLicenseExpired();
    }

    public function setLicenseExpiration(string $licenseExpiration): void
    {
        $this->licenseExpiration = $licenseExpiration;
    }

    public function getLicenseExpiration(string $format = ''): string
    {
        if (!$format ||
            !$this->licenseExpiration) {
            return $this->licenseExpiration;
        }

        $expiration = DateTime::createFromFormat('Ymd', $this->licenseExpiration);
        if (!$expiration) {
            return '';
        }

        return $expiration->format($format);
    }

    public function setLicenseUsedDomains(string $licenseUsedDomains): void
    {
        $this->licenseUsedDomains = $licenseUsedDomains;
    }

    public function getLicenseUsedDomains(): string
    {
        return $this->licenseUsedDomains;
    }

    public function setLicenseUsedDevDomains(string $licenseUsedDevDomains): void
    {
        $this->licenseUsedDevDomains = $licenseUsedDevDomains;
    }

    public function getLicenseUsedDevDomains(): string
    {
        return $this->licenseUsedDevDomains;
    }

    public function setDemoImport(array $demoImport): void
    {
        $this->demoImport = $demoImport;
    }

    public function getDemoImport(): array
    {
        return $this->demoImport;
    }

    public function isDevMode(): bool
    {
        return $this->isDevMode;
    }

    public function setIsDevMode(bool $isDevMode): void
    {
        $this->isDevMode = $isDevMode;
    }

    public function isNotCollapsedFieldsByDefault(): bool
    {
        return $this->isNotCollapsedFieldsByDefault;
    }

    public function setIsNotCollapsedFieldsByDefault(bool $isNotCollapsedFieldsByDefault): void
    {
        $this->isNotCollapsedFieldsByDefault = $isNotCollapsedFieldsByDefault;
    }

    public function isWithoutFieldsCollapseCursor(): bool
    {
        return $this->isWithoutFieldsCollapseCursor;
    }

    public function setIsWithoutFieldsCollapseCursor(bool $isWithoutFieldsCollapseCursor): void
    {
        $this->isWithoutFieldsCollapseCursor = $isWithoutFieldsCollapseCursor;
    }
}
