function get_expenses_json() {
    global $wpdb;

    // Query to aggregate expenses by date where confirmed is true
    $query = "
        SELECT 
            billdate AS date,
            SUM(orderTotal) AS sale
        FROM 
            {$wpdb->base_prefix}projects_details
        WHERE 
            confirmed = 1
        GROUP BY 
            billdate
        ORDER BY 
            billdate
    ";

    // Execute the query
    $results = $wpdb->get_results($query);

    // Format results as an array of objects
    $formatted_results = [];
    foreach ($results as $row) {
        $formatted_results[] = [
            'date' => $row->date,
            'sale' => floatval($row->sale), // Ensure sale is a float
        ];
    }

    // Return the results as JSON
    return json_encode($formatted_results);
}

// Example usage
header('Content-Type: application/json');
echo get_expenses_json();
