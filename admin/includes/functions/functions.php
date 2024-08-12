<?php

/* function to getTitle of page if page has var $page_title*/
function getTitle()
{
    // Use the global keyword to access the $page_title variable defined outside the function
    global $page_title;

    // Check if $page_title is set and not empty
    if (isset($page_title) && !empty($page_title)) {
        echo $page_title;
    } else {
        // Return a default title if $page_title is not set
        echo "Default Title";
    }
}

// home redirect fun

function homeRedirectfun($msg_error, $second_before_redirect = 2)
{
    // Display the error message
    echo "<p class='danger'>$msg_error</p>";

    // Redirect after the specified number of seconds
    header("Refresh: $second_before_redirect; url=admin_dashboard.php");

    // Ensure no further code is executed
    exit();
}

/* // Example usage
homeRedirectfun("An error occurred. You will be redirected shortly.", 5);
 */


//
function checkExistingUserEmail($conn, $username, $email, $user_id)
{
    try {
        // Initialize variables to track existing records
        $username_exists = false;
        $email_exists = false;

        // Check if username already exists (excluding current user's own data)
        $stmt_username = $conn->prepare('SELECT COUNT(*) AS count_username FROM users WHERE user_name = :username AND user_id != :user_id');
        $stmt_username->execute(['username' => $username, 'user_id' => $user_id]);
        $row_username = $stmt_username->fetch(PDO::FETCH_ASSOC);
        $username_exists = ($row_username['count_username'] > 0);

        // Check if email already exists (excluding current user's own data)
        $stmt_email = $conn->prepare('SELECT COUNT(*) AS count_email FROM users WHERE email = :email AND user_id != :user_id');
        $stmt_email->execute(['email' => $email, 'user_id' => $user_id]);
        $row_email = $stmt_email->fetch(PDO::FETCH_ASSOC);
        $email_exists = ($row_email['count_email'] > 0);

        // Return results
        return [
            'username_exists' => $username_exists,
            'email_exists' => $email_exists
        ];
    } catch (PDOException $e) {
        // Handle database errors if necessary
        return [
            'error' => true,
            'message' => "Database Error: " . $e->getMessage()
        ];
    }
}


// v2.0 function to check item exist or no for unique items like username,email,item_name,cat_name,etc..
/* 
HOW THIS FUN WORK -- ADD exclude_id when update only
Example Usage in Update:

Suppose you have a category with category_id = 5 and cat_name = "Books". You want to update the description but not change the name. If you pass 5 as the exclude_id, the function will exclude this category from the duplicate check. This means that only other categories with the name "Books" will be counted, avoiding a false positive for the current category.

Visual Example
Consider the following scenario:

Current Record: category_id = 5, cat_name = "Books"
Update Attempt: Change the description but keep the name as "Books".
Without exclusion, the check would count the record with category_id = 5 as a duplicate because it has the same name. By excluding this record, the function only counts other records with the name "Books", allowing the update to proceed.

*/
function checkItem($conn, $attribute_name, $table_name, $value_of_attribute, $exclude_id = null, $attribute_not_equal_exclude_id = null)
{
    try {
        // Initialize variables to track existing records
        $item_exist = false;

        // Prepare the SQL query
        $sql = "SELECT COUNT(*) AS count_item FROM `$table_name` WHERE `$attribute_name` = :value_of_attribute";

        // Add condition to exclude a specific ID if provided
        if ($exclude_id !== null) {
            $sql .= " AND `$attribute_not_equal_exclude_id` != :exclude_id";
        }

        // Prepare the statement
        $stmt = $conn->prepare($sql);

        // Bind the parameters
        $stmt->bindParam(':value_of_attribute', $value_of_attribute);
        if ($exclude_id !== null) {
            $stmt->bindParam(':exclude_id', $exclude_id, PDO::PARAM_INT);
        }

        // Execute the statement
        $stmt->execute();

        // Fetch the result
        $row_item = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the item exists
        $item_exist = ($row_item['count_item'] > 0);

        // Return results
        return [
            'item_exist' => $item_exist,
        ];
    } catch (PDOException $e) {
        // Handle database errors if necessary
        return [
            'error' => true,
            'message' => "Database Error: " . $e->getMessage()
        ];
    }
}


// Function to calc number of items items can be[ members,comments,items,pending members];

function calcNumberOfItems($conn, $item_name, $table_name, $condition = null, $val_of_condition = null)
{
    // Sanitize table and column names
    $table_name = preg_replace('/[^a-zA-Z0-9_]/', '', $table_name);
    $item_name = preg_replace('/[^a-zA-Z0-9_]/', '', $item_name);

    // Base query
    $query = "SELECT COUNT(`$item_name`) FROM `$table_name`";

    // Add condition if provided
    if ($condition !== null && $val_of_condition !== null) {
        // Sanitize condition
        $condition = preg_replace('/[^a-zA-Z0-9_]/', '', $condition);

        // Determine if value needs quotes
        if (is_numeric($val_of_condition)) {
            $query .= " WHERE `$condition` = :val_of_condition";
        } else {
            $query .= " WHERE `$condition` = :val_of_condition";
        }
    }

    // Prepare and execute statement
    $stm = $conn->prepare($query);

    // Bind value if condition is set
    if ($condition !== null && $val_of_condition !== null) {
        $stm->bindValue(':val_of_condition', $val_of_condition, is_numeric($val_of_condition) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }

    try {
        $stm->execute();
        $count = $stm->fetchColumn();
        return (int) $count;
    } catch (PDOException $e) {
        // Handle error
        error_log("Database error: " . $e->getMessage());
        return 0; // or handle it as appropriate
    }
}


// Function to get  of [Latest items || ALL ITEMS] items can be[ members,comments,items,pending members];
/* 
function getItems($conn, $table_name, $condition = null, $filterType = null, $specificDate = null, $limit = 5)
{
    $query = "SELECT * FROM `$table_name`";

    if ($filterType === 'day') {
        $query .= " WHERE DATE(`$condition`) = CURDATE()";
    } elseif ($filterType === 'month') {
        $query .= " WHERE MONTH(`$condition`) = MONTH(CURDATE()) AND YEAR(`$condition`) = YEAR(CURDATE())";
    } elseif ($filterType === 'specific_date' && $specificDate !== null) {
        $query .= " WHERE DATE(`$condition`) = :specificDate";
    } elseif ($filterType === 'specific_datetime' && $specificDate !== null) {
        $query .= " WHERE `$condition` = :specificDate";
    }

    // Append ORDER BY and LIMIT clauses
    // group_id= 0 meant get latest added members exclude admins
    if ($condition !== null) {
        $query .= "ORDER BY `$condition` DESC LIMIT :limit";
    }

    $stm = $conn->prepare($query);

    try {
        if (in_array($filterType, ['specific_date', 'specific_datetime']) && $specificDate !== null) {
            $stm->bindParam(':specificDate', $specificDate);
        }

        // Bind the limit parameter
        if ($condition !== null) {
            $stm->bindParam(':limit', $limit, PDO::PARAM_INT);
        }

        $stm->execute();
        $latestItems = $stm->fetchAll(PDO::FETCH_ASSOC);
        return $latestItems;
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return [];
    }
} 
*/

// getItems v2
/* function getItems(
    $conn,
    $table_name,
    $condition = null,
    $filterType = null,
    $specificDate = null,
    $limit = 5,
    $joinTable = null, // New parameter for the table to join
    $joinCondition = null, // New parameter for the join condition
    $attribute_name_of_join_table = null
) {
    // Start building the query
    $query = "SELECT ";

    // Include specific columns if a join is specified
    if ($joinTable !== null && $joinCondition !== null) {
        $query .= "items.*, $joinTable.$attribute_name_of_join_table "; // Adjust column names as needed
    } else {
        $query .= "* ";
    }

    $query .= "FROM `$table_name` AS items ";

    // Add the JOIN clause if specified
    if ($joinTable !== null && $joinCondition !== null) {
        $query .= "JOIN `$joinTable` ON $joinCondition ";
    }

    // Add filtering conditions
    if ($filterType === 'day') {
        $query .= "WHERE DATE(`$condition`) = CURDATE() ";
    } elseif ($filterType === 'month') {
        $query .= "WHERE MONTH(`$condition`) = MONTH(CURDATE()) AND YEAR(`$condition`) = YEAR(CURDATE()) ";
    } elseif ($filterType === 'specific_date' && $specificDate !== null) {
        $query .= "WHERE DATE(`$condition`) = :specificDate ";
    } elseif ($filterType === 'specific_datetime' && $specificDate !== null) {
        $query .= "WHERE `$condition` = :specificDate ";
    }

    // Append ORDER BY and LIMIT clauses
    if ($condition !== null) {
        $query .= "ORDER BY `$condition` DESC LIMIT :limit";
    }

    $stm = $conn->prepare($query);

    try {
        // Bind parameters for filtering by specific date or datetime
        if (in_array($filterType, ['specific_date', 'specific_datetime']) && $specificDate !== null) {
            $stm->bindParam(':specificDate', $specificDate);
        }

        // Bind the limit parameter
        if ($condition !== null) {
            $stm->bindParam(':limit', $limit, PDO::PARAM_INT);
        }

        $stm->execute();
        $latestItems = $stm->fetchAll(PDO::FETCH_ASSOC);
        return $latestItems;
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return [];
    }
}
 */

// GetItems v3
/* function getItems(
    $conn,
    $table_name,
    $condition = null,
    $filterType = null,
    $specificDate = null,
    $limit = 5,
    $joins = [] // New parameter for multiple joins
) {
    // Start building the query
    $query = "SELECT items.*";

    // Include specific columns from joined tables
    if (!empty($joins)) {
        foreach ($joins as $join) {
            if (isset($join['table']) && isset($join['attribute'])) {
                $query .= ", {$join['table']}.{$join['attribute']}";
            }
        }
    }

    $query .= " FROM `$table_name` AS items";

    // Add the JOIN clauses if specified
    if (!empty($joins)) {
        foreach ($joins as $join) {
            if (isset($join['table']) && isset($join['condition'])) {
                $query .= " JOIN `{$join['table']}` ON {$join['condition']}";
            }
        }
    }

    // Add filtering conditions
    if ($filterType === 'day') {
        $query .= " WHERE DATE(`$condition`) = CURDATE()";
    } elseif ($filterType === 'month') {
        $query .= " WHERE MONTH(`$condition`) = MONTH(CURDATE()) AND YEAR(`$condition`) = YEAR(CURDATE())";
    } elseif ($filterType === 'specific_date' && $specificDate !== null) {
        $query .= " WHERE DATE(`$condition`) = :specificDate";
    } elseif ($filterType === 'specific_datetime' && $specificDate !== null) {
        $query .= " WHERE `$condition` = :specificDate";
    }

    // Append ORDER BY and LIMIT clauses
    if ($condition !== null) {
        $query .= " ORDER BY `$condition` DESC LIMIT :limit";
    }

    $stm = $conn->prepare($query);

    try {
        // Bind parameters for filtering by specific date or datetime
        if (in_array($filterType, ['specific_date', 'specific_datetime']) && $specificDate !== null) {
            $stm->bindParam(':specificDate', $specificDate);
        }

        // Bind the limit parameter
        if ($condition !== null) {
            $stm->bindParam(':limit', $limit, PDO::PARAM_INT);
        }

        $stm->execute();
        $latestItems = $stm->fetchAll(PDO::FETCH_ASSOC);
        return $latestItems;
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return [];
    }
} */

//GETITEMS V4
/* function getItems(
    $conn,
    $table_name,
    $condition = null,
    $filterType = null,
    $specificDate = null,
    $limit = 5,
    $joins = [], // New parameter for multiple joins
    $item_id = null,
    $attribute_of_item_id = null,
    $attribute_name = null,
    $val_of_attr_name = null,
) {
    // Start building the query
    $query = "SELECT $table_name.*";

    // Include specific columns from joined tables
    if (!empty($joins)) {
        foreach ($joins as $join) {
            if (isset($join['table']) && isset($join['attribute'])) {
                $query .= ", {$join['table']}.{$join['attribute']}";
            }
        }
    }

    $query .= " FROM `$table_name`";

    // Add the JOIN clauses if specified
    if (!empty($joins)) {
        foreach ($joins as $join) {
            if (isset($join['table']) && isset($join['condition'])) {
                $query .= " JOIN `{$join['table']}` ON {$join['condition']}";
            }
        }
    }

    // Add filtering conditions
    if ($filterType === 'day') {
        $query .= " WHERE DATE(`$condition`) = CURDATE()";
    } elseif ($filterType === 'item_id') {
        $query .= " WHERE $table_name.$attribute_of_item_id = :item_id";
    } elseif ($filterType === 'condition') {
        $query .= " WHERE $table_name.$attribute_name= :val_of_attr_name";
    } elseif ($filterType === 'month') {
        $query .= " WHERE MONTH(`$condition`) = MONTH(CURDATE()) AND YEAR(`$condition`) = YEAR(CURDATE())";
    } elseif ($filterType === 'specific_date' && $specificDate !== null) {
        $query .= " WHERE DATE(`$condition`) = :specificDate";
    } elseif ($filterType === 'specific_datetime' && $specificDate !== null) {
        $query .= " WHERE `$condition` = :specificDate";
    }

    // Append ORDER BY and LIMIT clauses
    if ($condition !== null) {
        $query .= " ORDER BY `$condition` DESC";
    }

    if ($limit !== null) {
        $query .= " LIMIT :limit";
    }

    $stm = $conn->prepare($query);

    try {
        // Bind parameters for filtering by specific date or datetime
        if (in_array($filterType, ['specific_date', 'specific_datetime']) && $specificDate !== null) {
            $stm->bindParam(':specificDate', $specificDate);
        }

        // Bind the limit parameter
        if ($limit !== null) {
            $stm->bindParam(':limit', $limit, PDO::PARAM_INT);
        }
        if ($item_id !== null) {
            $stm->bindParam(':item_id', $item_id, PDO::PARAM_INT);
        }
        if ($val_of_attr_name !== null) {
            $stm->bindParam(':val_of_attr_name', $val_of_attr_name);
        }

        $stm->execute();
        $items = $stm->fetchAll(PDO::FETCH_ASSOC);
        return $items;
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return [];
    }
}
 */

// getitems v5
function getItems(
    $conn,
    $table_name,
    $condition = null,
    $filterType = null,
    $specificDate = null,
    $limit = 5,
    $joins = [], // New parameter for multiple joins
    $item_id = null,
    $attribute_of_item_id = null,
    $additionalConditions = [] // New parameter for additional dynamic conditions
) {
    // Start building the query
    $query = "SELECT $table_name.*";

    // Include specific columns from joined tables
    if (!empty($joins)) {
        foreach ($joins as $join) {
            if (isset($join['table']) && isset($join['attribute'])) {
                $query .= ", {$join['table']}.{$join['attribute']}";
            }
        }
    }

    $query .= " FROM `$table_name`";

    // Add the JOIN clauses if specified
    if (!empty($joins)) {
        foreach ($joins as $join) {
            if (isset($join['table']) && isset($join['condition'])) {
                $query .= " JOIN `{$join['table']}` ON {$join['condition']}";
            }
        }
    }

    // Add filtering conditions
    $whereClauses = [];
    $params = [];

     // Add dynamic conditions
     if (!empty($additionalConditions)) {
        foreach ($additionalConditions as $key => $value) {
            $whereClauses[] = "$table_name.$key = :$key";
            $params[":$key"] = $value;
        }
    }

    if ($filterType === 'day') {
        $whereClauses[] = "DATE(`$condition`) = CURDATE()";
    } elseif ($filterType === 'item_id') {
        $whereClauses[] = "$table_name.$attribute_of_item_id = :item_id";
        $params[':item_id'] = $item_id;
    } elseif ($filterType === 'month') {
        $whereClauses[] = "MONTH(`$condition`) = MONTH(CURDATE()) AND YEAR(`$condition`) = YEAR(CURDATE())";
    } elseif ($filterType === 'specific_date' && $specificDate !== null) {
        $whereClauses[] = "DATE(`$condition`) = :specificDate";
        $params[':specificDate'] = $specificDate;
    } elseif ($filterType === 'specific_datetime' && $specificDate !== null) {
        $whereClauses[] = "`$condition` = :specificDate";
        $params[':specificDate'] = $specificDate;
    }

    if (!empty($whereClauses)) {
        $query .= " WHERE " . implode(" AND ", $whereClauses);
    }

    // Append ORDER BY and LIMIT clauses
    if ($condition !== null) {
        $query .= " ORDER BY `$condition` DESC";
    }

    if ($limit !== null) {
        $query .= " LIMIT :limit";
        $params[':limit'] = $limit;
    }

    $stm = $conn->prepare($query);

    try {
        // Bind parameters
        foreach ($params as $key => &$val) {
            if ($key === ':limit') {
                $stm->bindParam($key, $val, PDO::PARAM_INT);
            } else {
                $stm->bindParam($key, $val);
            }
        }

        $stm->execute();
        $items = $stm->fetchAll(PDO::FETCH_ASSOC);
        return $items;
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return [];
    }
}
