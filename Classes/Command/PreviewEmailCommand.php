<?php

namespace Slub\DigasFeManagement\Command;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2024 SLUB Dresden <typo3@slub-dresden.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License is available at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\TypoScript\TemplateService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Request as ExtbaseRequest;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Send preview emails for all digas-fe-management email templates to Mailpit.
 */
class PreviewEmailCommand extends Command
{
    /**
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

    public function __construct(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
        parent::__construct();
    }

    protected const TEMPLATE_PATHS = [
        'EXT:digas_fe_management/Resources/Private/Extensions/femanager/Templates/',
        'EXT:femanager/Resources/Private/Templates/',
    ];

    protected const PARTIAL_PATHS = [
        'EXT:digas_fe_management/Resources/Private/Partials/',
        'EXT:femanager/Resources/Private/Partials/',
    ];

    protected const LAYOUT_PATHS = [
        'EXT:femanager/Resources/Private/Layouts/',
    ];

    protected const NOTIFICATION_TEMPLATE_PATHS = [
        'html' => 'EXT:digas_fe_management/Resources/Private/Templates/Email/Html/',
        'text' => 'EXT:digas_fe_management/Resources/Private/Templates/Email/Text/',
    ];

    protected function configure(): void
    {
        $this->setDescription('[DiGA.Sax FE Management] Send all email template previews to Mailpit.')
            ->addArgument(
                'to',
                InputArgument::OPTIONAL,
                'Recipient email address for preview.',
                'test-preview@example.com'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());

        $this->initializeSiteRequest();
        $this->initializeTypoScriptFrontend();

        $to = $input->getArgument('to');

        $dummyUser = $this->getDummyUser();
        $dummyDocuments = $this->getDummyDocuments();
        $dummySettings = $this->getDummySettings();
        $dummyNotificationDocuments = $this->getDummyNotificationDocuments();

        $emails = [
            'RequestKitodoAccess' => [
                'subject' => 'RequestKitodoAccess (Admin)',
                'template' => 'RequestKitodoAccess',
                'variables' => [
                    'user' => $dummyUser,
                    'documents' => $dummyDocuments,
                    'message' => 'Dies ist eine Testnachricht vom Nutzer.',
                    'settings' => $dummySettings,
                ],
            ],
            'RequestKitodoAccessUser' => [
                'subject' => 'RequestKitodoAccessUser (Nutzer)',
                'template' => 'RequestKitodoAccessUser',
                'variables' => [
                    'user' => $dummyUser,
                    'documents' => $dummyDocuments,
                    'message' => 'Dies ist eine Testnachricht vom Nutzer.',
                    'settings' => $dummySettings,
                ],
            ],
            'CreateUserNotifyAfterPwChange' => [
                'subject' => 'CreateUserNotifyAfterPwChange (Nutzer)',
                'template' => 'CreateUserNotifyAfterPwChange',
                'extensionName' => 'Femanager',
                'variables' => [
                    'user' => $dummyUser,
                    'settings' => $dummySettings,
                    'hash' => 'dummy-hash-1234abcd',
                ],
            ],
            'CreateUserConfirmation' => [
                'subject' => 'CreateUserConfirmation (Registrierung)',
                'template' => 'CreateUserConfirmation',
                'extensionName' => 'Femanager',
                'variables' => [
                    'user' => $dummyUser,
                    'settings' => $dummySettings,
                    'hash' => 'dummy-hash-1234abcd',
                ],
            ],
            'ConfirmDisableAction' => [
                'subject' => 'ConfirmDisableAction (Konto deaktivieren)',
                'template' => 'ConfirmDisableAction',
                'variables' => [
                    'user' => $dummyUser,
                    'settings' => $dummySettings,
                    'hash' => 'dummy-hash-1234abcd',
                ],
            ],
            'Invitation' => [
                'subject' => 'Invitation (Einladung)',
                'template' => 'Invitation',
                'extensionName' => 'Femanager',
                'variables' => [
                    'user' => $dummyUser,
                    'settings' => $dummySettings,
                    'hash' => 'dummy-hash-1234abcd',
                ],
            ],
        ];

        $notificationEmails = [
            'KitodoAccessGrantedNotification' => [
                'subject' => 'KitodoAccessGrantedNotification (Zugang genehmigt/abgelehnt)',
                'variables' => [
                    'loginUrl' => 'https://v11-diga-sax.ddev.site/anmeldung',
                    'documentsList' => $dummyNotificationDocuments,
                ],
            ],
            'KitodoAccessExpirationNotification' => [
                'subject' => 'KitodoAccessExpirationNotification (Zugang läuft ab)',
                'variables' => [
                    'loginUrl' => 'https://v11-diga-sax.ddev.site/anmeldung',
                    'documentsList' => $dummyNotificationDocuments,
                ],
            ],
            'RemindUnusedAccounts' => [
                'subject' => 'RemindUnusedAccounts (Inaktiver Account)',
                'variables' => [
                    'loginUrl' => 'https://v11-diga-sax.ddev.site/anmeldung',
                    'unusedTimespan' => 180,
                    'deleteTimespan' => 365,
                ],
            ],
        ];

        // Build typo3Language → SiteLanguage map from configured site
        $siteLanguageMap = [];
        try {
            $sites = GeneralUtility::makeInstance(SiteFinder::class)->getAllSites();
            $site = reset($sites);
            if ($site) {
                foreach ($site->getLanguages() as $siteLanguage) {
                    $siteLanguageMap[$siteLanguage->getTypo3Language()] = $siteLanguage;
                }
            }
        } catch (\Throwable $e) {
            // continue without site language map
        }

        $languages = ['de' => 'de', 'en' => 'default'];

        $sentCount = 0;
        $totalCount = count($emails) * count($languages)
            + count($notificationEmails) * count($languages);

        foreach ($languages as $langLabel => $langKey) {
            // Set SiteLanguage on request so LocalizationUtility picks up the correct language
            if (isset($siteLanguageMap[$langKey], $GLOBALS['TYPO3_REQUEST'])) {
                $GLOBALS['TYPO3_REQUEST'] = $GLOBALS['TYPO3_REQUEST']->withAttribute('language', $siteLanguageMap[$langKey]);
            }
            $GLOBALS['LANG'] = GeneralUtility::makeInstance(LanguageServiceFactory::class)->create($langKey);
            if (isset($GLOBALS['TSFE'])) {
                $GLOBALS['TSFE']->lang = $langKey;
            }
            $io->section(strtoupper($langLabel));
            foreach ($emails as $name => $config) {
                try {
                    $html = $this->renderTemplate(
                        $config['template'],
                        $config['variables'],
                        $config['extensionName'] ?? null
                    );
                    $subject = sprintf('[Preview - %s] %s', strtoupper($langLabel), $config['subject']);
                    $this->sendEmail($to, $subject, $html);
                    $io->text(sprintf('  ✓ %s → %s', $name, $to));
                    $sentCount++;
                } catch (\Throwable $e) {
                    $io->warning(sprintf('  ✗ %s failed: %s', $name, $e->getMessage()));
                }
            }
            foreach ($notificationEmails as $name => $config) {
                $variables = array_merge($config['variables'], ['languageKey' => $langLabel]);
                try {
                    $html = $this->renderNotificationTemplate($name, $variables, 'html');
                    $text = $this->renderNotificationTemplate($name, $variables, 'text');
                    $subject = sprintf('[Preview - %s] %s', strtoupper($langLabel), $config['subject']);
                    $this->sendMultipartEmail($to, $subject, $html, $text);
                    $io->text(sprintf('  ✓ %s → %s', $name, $to));
                    $sentCount++;
                } catch (\Throwable $e) {
                    $io->warning(sprintf('  ✗ %s failed: %s', $name, $e->getMessage()));
                }
            }
        }

        $io->success(sprintf('%d/%d preview emails sent. Check Mailpit: https://v11-diga-sax.ddev.site:8026', $sentCount, $totalCount));

        return Command::SUCCESS;
    }

    protected function renderTemplate(string $templateName, array $variables, ?string $extensionName = null): string
    {
        $view = GeneralUtility::makeInstance(StandaloneView::class);

        $templatePaths = array_map(
            static fn($path) => GeneralUtility::getFileAbsFileName($path),
            self::TEMPLATE_PATHS
        );
        $partialPaths = array_map(
            static fn($path) => GeneralUtility::getFileAbsFileName($path),
            self::PARTIAL_PATHS
        );
        $layoutPaths = array_map(
            static fn($path) => GeneralUtility::getFileAbsFileName($path),
            self::LAYOUT_PATHS
        );

        $view->setTemplateRootPaths($templatePaths);
        $view->setPartialRootPaths($partialPaths);
        $view->setLayoutRootPaths($layoutPaths);
        $view->setTemplate('Email/' . $templateName);
        $view->assignMultiple($variables);

        if ($extensionName !== null) {
            $absoluteTemplatePath = $this->resolveTemplatePath('Email/' . $templateName . '.html');
            $view->setTemplatePathAndFilename($absoluteTemplatePath);

            $extbaseRequest = GeneralUtility::makeInstance(ExtbaseRequest::class);
            $extbaseRequest->setControllerExtensionName($extensionName);
            $view->getRenderingContext()->setRequest($extbaseRequest);
        }

        return $view->render();
    }

    protected function renderNotificationTemplate(string $templateName, array $variables, string $format = 'html'): string
    {
        $basePath = GeneralUtility::getFileAbsFileName(self::NOTIFICATION_TEMPLATE_PATHS[$format]);
        $templateFile = $basePath . $templateName . '.html';
        if (!file_exists($templateFile)) {
            throw new \RuntimeException(sprintf('Notification template not found: %s', $templateFile));
        }

        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setFormat($format === 'text' ? 'txt' : 'html');
        $view->setTemplatePathAndFilename($templateFile);
        $view->assignMultiple($variables);

        return $view->render();
    }

    protected function sendMultipartEmail(string $to, string $subject, string $html, string $text): void
    {
        $email = GeneralUtility::makeInstance(MailMessage::class);
        $email->setTo($to)
            ->setFrom('noreply@diga-sax.de')
            ->setSubject($subject)
            ->html($html)
            ->text($text)
            ->send();
    }

    protected function sendEmail(string $to, string $subject, string $content, string $format = 'html'): void
    {
        $email = GeneralUtility::makeInstance(MailMessage::class);
        $email->setTo($to)
            ->setFrom('noreply@diga-sax.de')
            ->setSubject($subject);
        if ($format === 'text') {
            $email->text($content);
        } else {
            $email->html($content);
        }
        $email->send();
    }

    protected function resolveTemplatePath(string $relativeTemplatePath): string
    {
        foreach (self::TEMPLATE_PATHS as $basePath) {
            $absolute = GeneralUtility::getFileAbsFileName($basePath . $relativeTemplatePath);
            if (file_exists($absolute)) {
                return $absolute;
            }
        }
        throw new \RuntimeException('Template not found: ' . $relativeTemplatePath);
    }

    protected function getDummyUser(): array
    {
        return [
            'uid' => 9999,
            'username' => 'max.mustermann',
            'firstname' => 'Max',
            'lastname' => 'Mustermann',
            'email' => 'max.mustermann@example.com',
            'name' => 'Max Mustermann',
            'company' => 'Testfirma GmbH',
            'address' => 'Musterstraße 1',
            'zip' => '01234',
            'city' => 'Dresden',
        ];
    }

    protected function getDummyDocuments(): array
    {
        return [
            [
                'recordId' => 'TEST-DOC-001',
                'title' => 'Testdokument Eins: Versickerungsversuch',
                'dlfDocument' => [
                    'recordId' => 'TEST-DOC-001',
                    'title' => 'Testdokument Eins: Versickerungsversuch',
                ],
            ],
            [
                'recordId' => 'TEST-DOC-002',
                'title' => 'Testdokument Zwei: Graustein Nahseismik',
                'dlfDocument' => [
                    'recordId' => 'TEST-DOC-002',
                    'title' => 'Testdokument Zwei: Graustein Nahseismik',
                ],
            ],
        ];
    }

    protected function getDummyNotificationDocuments(): array
    {
        return [
            [
                'recordId' => 'TEST-DOC-001',
                'documentTitle' => 'Versickerungsversuch Hoyerswerda Süd, 1963',
                'endTime' => mktime(0, 0, 0, 12, 31, (int)date('Y') + 1),
                'rejected' => false,
                'rejectedReason' => '',
            ],
            [
                'recordId' => 'TEST-DOC-002',
                'documentTitle' => 'Graustein Nahseismik, 1971',
                'endTime' => 0,
                'rejected' => true,
                'rejectedReason' => 'Das Dokument ist für den angegebenen Nutzungszweck nicht zugänglich.',
            ],
        ];
    }

    protected function getDummySettings(): array
    {
        return [
            'adminEmail' => 'digasax-bohrarchiv@slub-dresden.de',
            'adminName' => 'DiGA.Sax - Administrator',
            'pids' => [
                'rootPage' => 1,
                'loginPage' => 41,
            ],
        ];
    }

    protected function initializeTypoScriptFrontend(): void
    {
        if (isset($GLOBALS['TSFE'])) {
            return;
        }
        try {
            $tsConfig = $this->configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
            );
            $tsfe = GeneralUtility::makeInstance(TypoScriptFrontendController::class, null, 1, 0);
            $tsfe->tmpl = GeneralUtility::makeInstance(TemplateService::class);
            $tsfe->tmpl->setup = $tsConfig;
            $tsfe->cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class, $tsfe);
            $GLOBALS['TSFE'] = $tsfe;
        } catch (\Throwable $e) {
            // TSFE initialization failed – f:cObject-based ViewHelpers may not work.
        }
    }

    protected function initializeSiteRequest(): void
    {
        if (isset($GLOBALS['TYPO3_REQUEST'])) {
            return;
        }
        try {
            $sites = GeneralUtility::makeInstance(SiteFinder::class)->getAllSites();
            $site = reset($sites);
            $baseUrl = $site ? (string)$site->getBase() : 'https://v11-diga-sax.ddev.site';
        } catch (\Throwable $e) {
            $baseUrl = 'https://v11-diga-sax.ddev.site';
        }
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest($baseUrl))
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE);
    }
}
