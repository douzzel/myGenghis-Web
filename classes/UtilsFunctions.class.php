<?php

    class UtilsFunctions
    {
        public static function className(array $class, string $table): string
        {
            $className = "";
            if (count($class) > 0) {
                $i = 0;
                foreach ($class as $value) {
                    $className .= $i=== 0 ? "Entity".ucfirst($value) : ucfirst($value);
                    $i++;
                }
            } else {
                $className = "Entity".ucfirst($table);
            }
            return $className;
        }
    }
