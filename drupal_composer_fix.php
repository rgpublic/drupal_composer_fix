#!/usr/bin/php
<?php
function ffail($s) {
    echo "\e[91m".$s."\e[0m\n\n";
    exit;
}

if (!is_file("composer.json")) ffail("composer.json not found.");
if (!is_file("core/composer.json")) ffail("core/composer.json not found.");

$json=json_decode(file_get_contents("composer.json"),true);
if (isset($json["replace"]["drupal/core"])) {
    if (isset($json["require"]["drupal/core"])) ffail("drupal/core unexpected in require section of composer.json");
    $json["require"]["drupal/core"]=$json["replace"]["drupal/core"];
    unset($json["replace"]["drupal/core"]);
    if (!$json["replace"]) unset($json["replace"]);
    file_put_contents("composer.json",json_encode($json,JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ));

    echo "\e[92mcomposer.json fixed successfully.\e[0m\n";
} else {
    if (!isset($json["require"]["drupal/core"])) ffail("drupal/core neither in replace nor require section. Is this really a Drupal installation?");
    echo "\e[94mcomposer.json seems to be fixed already.\e[0m\n";
}

$json=json_decode(file_get_contents("core/composer.json"),true);
if (isset($json["replace"])) {
    if (!isset($json["require"])) ffail("core/composer.json doesn't have a require section.");
    if (!is_array($json["require"])) ffail("core/composer.json has an invalid require section (no array).");
    foreach ($json["replace"] as &$item) {
        $item="@dev";
    } unset($item);
    $json["require"]=array_merge($json["require"],$json["replace"]);
    unset($json["replace"]);
    file_put_contents("core/composer.json",json_encode($json,JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ));

    echo "\e[92mcore/composer.json fixed successfully.\e[0m\n";
} else {
    echo "\e[94mcore/composer.json seems to be fixed already.\e[0m\n";
}

echo "\n";
