<?php

namespace Ubirimi;

use Ubirimi\Container\UbirimiContainer;
use Ubirimi\Service\ConfigService;
use Ubirimi\ServiceProvider\UbirimiCoreServiceProvider;
use Composer\Script\Event;

class Setup {

    public static function setup(Event $event) {

        /* parse .properties file and make them available in the container */
        $configsApplication = ConfigService::process(__DIR__ . '/../../app/config/app.properties');
        $configsDatabase = ConfigService::process(__DIR__ . '/../../app/config/db.properties');
        $configsSMTP = ConfigService::process(__DIR__ . '/../../app/config/smtp.properties');
        $configsSubversion = ConfigService::process(__DIR__ . '/../../app/config/subversion.properties');
        $configsSubversion = ConfigService::process(__DIR__ . '/../../app/config/rabbitmq.properties');

        $configs = array_merge($configsApplication, $configsDatabase, $configsSMTP, $configsSubversion);

        /* register global configs to the container */
        UbirimiContainer::loadConfigs($configs);
        UbirimiContainer::register(new UbirimiCoreServiceProvider());

        $io = $event->getIO();

        $adminFirstName = $io->ask("Administrator First Name: ");
        $adminLastName = $io->ask("Administrator Last Name: ");
        $adminUsername = $io->ask("Administrator Username: ");
        $adminPassword = $io->ask("Administrator Password: ");
        $adminEmail = $io->ask("Administrator Password (again): ");
        $baseURL = "http://ubirimi.dev";

        $clientData = array(
            'adminFirstName' => $adminFirstName,
            'adminLastName' => $adminLastName,
            'adminUsername' => $adminUsername,
            'adminPass' => $adminPassword,
            'adminEmail' => $adminEmail,
            'baseURL' => $baseURL
        );

        UbirimiContainer::get()['client']->add($clientData);

        exit;
    }
}