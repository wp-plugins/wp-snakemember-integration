<?php


function wp_sm_add_activerecord_classes_folder($path_to_classes_folder){
    ActiveRecord\Config::instance()->add_model_directory($path_to_classes_folder);
}

wp_sm_add_activerecord_classes_folder(realpath(dirname(__FILE__))."/../models/");