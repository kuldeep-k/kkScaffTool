<?php
return array(
    "Application" => array ( 
        "UserGroup" => array ( 
            "list" => array (
                "show_columns" => array(
                    "user_id" => array (
                        "key-column" => "sf_guard_user|id",
                        "value-column" => "sf_guard_user|username",
                    ),
                    "group_id" => array (
                        "key-column" => "sf_guard_group|id",
                        "value-column" => "sf_guard_group|name",
                    ), 
                )
            ),
            "form" => array (
                "widgets" => array(
                    "user_id" => array (
                        "type" => "select",
                        "default" => "Select User", 
                        "table" => "sf_guard_user",
                        "key-column" => "id",
                        "value-column" => "username",
                    ),
                    "group_id" => array (
                        "type" => "select",
                        "default" => "Select Group",
                        "table" => "sf_guard_group", 
                        "key-column" => "id",
                        "value-column" => "name",
                    ) 
                )
            )
        )
    )        
);
