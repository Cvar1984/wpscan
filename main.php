<?php

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;


$app = new Application();
$app->register('scan-theme')
    ->addArgument('url', InputArgument::REQUIRED, 'Path to url list')
    ->addArgument('timeout', InputArgument::OPTIONAL, 'Default timeout  3s', 3)
    ->setCode(function (InputInterface $input, OutputInterface $output): int {
        $themeList = file('themes.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $urls = file($input->getArgument('url'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $ch = curl_init();
        foreach ($urls as $url) {
            $output->writeln(sprintf('%sTesting URL: %s', PHP_EOL, $url));

            foreach ($themeList as $themeName) {
                $url = "$url/wp-content/themes/$themeName/style.css";
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
                curl_setopt($ch, CURLOPT_TIMEOUT, $input->getArgument('timeout'));
                $result = curl_exec($ch);
                preg_match('/Version:\s(.*)/m', $result, $matches);

                if (isset ($matches[1])) {
                    $output->writeln(sprintf('<info>%s: %s</info>', $themeName, $matches[1]));
                    continue;
                }
                $output->writeln(sprintf('<error>%s Not found</error>', $themeName));
            }
        }
        curl_close($ch);
        return Command::SUCCESS;
    }
);

$app->register('scan-plugin')
    ->addArgument('url', InputArgument::REQUIRED, 'Path to url list')
    ->addArgument('timeout', InputArgument::OPTIONAL, 'Default timeout  3s', 3)
    ->setCode(function (InputInterface $input, OutputInterface $output): int {
        $pluginList = file('plugins.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $urls = file($input->getArgument('url'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $ch = curl_init();
        foreach ($urls as $url) {
            $output->writeln(sprintf('%sTesting URL: %s', PHP_EOL, $url));

            foreach ($pluginList as $pluginName) {
                $url = "$url/wp-content/themes/$pluginName/style.css";
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
                curl_setopt($ch, CURLOPT_TIMEOUT, $input->getArgument('timeout'));
                $result = curl_exec($ch);
                preg_match('/Stable\stag:\s(.*)/m', $result, $matches);

                if (isset ($matches[1])) {
                    $output->writeln(sprintf('<info>%s: %s</info>', $pluginName, $matches[1]));
                    continue;
                }
                $output->writeln(sprintf('<error>%s Not found</error>', $pluginName));
            }
        }
        curl_close($ch);
        return Command::SUCCESS;
    }
);
$app->run();