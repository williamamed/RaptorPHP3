<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Raptor\Component\systemBundle\Command;

use Doctrine\ORM\Mapping\MappingException;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

/**
 * Description of MigrateCommand
 *
 * @author william.tamayo
 */
class MigrateCommand extends Command {

    protected function configure() {
        $this
                ->setName('raptor3:migrate')
                ->setDescription('Migra los componentes de la serie 2 hacia la serie 3')
                ->setHelp(<<<EOT
El comando <info>raptor3:migrate</info> Migra los componentes de la serie 2 hacia la serie 3
EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $src = \Raptor\Core\Location::get(\Raptor\Core\Location::SRC);
        $files = \Raptor\Util\Files::find($src , '*.php');
        $result = "";
        $output->writeln("--------------------------------------------------------------");
        $output->writeln("Buscando archivos fuentes... ");
        $output->writeln("--------------------------------------------------------------");
        foreach ($files as $value) {
            $modify=false;
            $values = array();
            $result = file_get_contents($value);
            preg_match_all('/@Route\s*(\/\S*)*\n/', $result, $values);
            
            foreach ($values[0] as $key => $match) {

                $val = $values[1][$key];
                //$output->writeln($match);
                $modify=true;
                $result = preg_replace('/' . str_replace('/', '\/', $match) . '/', '@Route("' . $val . '")' . "\n", $result);
                
            }
            if ($modify && count(explode('use Raptor\Bundle\Annotations\Route;', $result)) == 1) {
                $uses = array();
                preg_match('/namespace (\S*)/', $result, $uses);
                if (count($uses) > 0) {
                    $result = str_replace($uses[0], $uses[0] . "\n\nuse Raptor\\Bundle\\Annotations\\Route;", $result);
                }
            }
            if($modify){
                $output->writeln("Migrando: ".  basename($value));
                file_put_contents($value,$result);
            }
        }
        $output->writeln("");
        $output->writeln("--------------------------------------------------------------");
        $output->writeln("Analizando los metadatos de instalacion de componentes... ");
        $output->writeln("--------------------------------------------------------------");
        $bundles = \Raptor\Util\Files::find($src , '*Bundle');
        foreach ($bundles as $dir) {
            if (is_dir($dir)) {
                $manifest = \Raptor\Util\Files::find($dir, 'install.json');
                if (file_exists($dir . '/' . basename($dir) . '.php')) {
                    $jsonFile=$dir."/Manifest/install.json";
                    $json = array(
                        'author' => 'Autor del componente',
                        'name' => basename($dir),
                        'description' => '...',
                        'namespace' => basename(dirname($dir)) . '.' . basename($dir) . '.' . basename($dir),
                        'version' => '0.0.1',
                        'require' => array('system' => '>=@3.0.1')
                    );
                    if (count($manifest) > 0) {
                        $json = array_merge($json, json_decode(file_get_contents($manifest[0]), true));
                        $jsonFile=$manifest[0];
                    }
                    $output->writeln("Modificando manifiesto(install.json): ".basename($dir));
                    if(!file_exists(dirname($jsonFile)))
                        @mkdir (dirname($jsonFile));
                    file_put_contents($jsonFile, json_encode($json, JSON_PRETTY_PRINT));
                    //$output->writeln($json);
                }
            }
        }
    }

}
