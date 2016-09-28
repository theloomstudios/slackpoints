<?php

namespace Neon {

    class Database {

        public function get_data($query, $parameters, $debug = true) {

            // Create mysqli
            $mysqli = new \mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

            if ($mysqli->connect_error) {
                return 'Connect Error: ' . $mysqli->connect_error;
            }

            // Prepare query
            $query = preg_replace_callback("/:([a-z_]+)/", function($matches) use ($parameters, $mysqli) {
                $value = $parameters[$matches[1]];
                if (isset($value)) {
                    if (is_int($value)) return $value;
                    return "'".$mysqli->escape_string($value)."'";
                }
                return 'NULL';
            }, $query);

            // Debug
            if (DB_DEBUG_OUTPUT) echo $query . PHP_EOL;
            if (DB_DEBUG_DATABASE && $debug) \Neon\Debug::save($query);

            // Result sets
            $result_sets = array();

            // Execute multi query
            if ($mysqli->multi_query($query)) {

                do {

                    // Store first result set
                    if ($result = $mysqli->store_result()) {
                        $result_set = array();
                        while ($row = $result->fetch_assoc()) {
                            $result_set[] = $row;
                        }
                        $result_sets[] = $result_set;
                        $result->free();
                    }

                } while (@$mysqli->next_result()); // suppress warnings
            }

            // Close connection
            $mysqli->close();

            // Return
            return $result_sets;

        }

    }

}