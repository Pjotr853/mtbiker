<!DOCTYPE html>
<html>
<body>

<?php
// Pripojenie na databázu
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'mtbiker';

// Vytvorenie pripojenia
$conn = new mysqli($host, $user, $password, $dbname);

// Kontrola pripojenia
if ($conn->connect_error) {
    die("Spojenie zlyhalo: " . $conn->connect_error);
}

//**************************************  SQL dotaz na získanie údajov ************************************************//
$sql = "
    SELECT
        SUM(CASE WHEN price = 0 THEN quantity ELSE 0 END) AS quantity_price_0,
        SUM(CASE WHEN price = 0 THEN price * quantity ELSE 0 END) AS total_sales_price_0,

        SUM(CASE WHEN price > 0 AND price <= 1 THEN quantity ELSE 0 END) AS quantity_price_0_to_1,
        SUM(CASE WHEN price > 0 AND price <= 1 THEN price * quantity ELSE 0 END) AS total_sales_price_0_to_1,

        SUM(CASE WHEN price > 1 AND price <= 10 THEN quantity ELSE 0 END) AS quantity_price_1_to_10,
        SUM(CASE WHEN price > 1 AND price <= 10 THEN price * quantity ELSE 0 END) AS total_sales_price_1_to_10,

        SUM(CASE WHEN price > 10 AND price <= 100 THEN quantity ELSE 0 END) AS quantity_price_10_to_100,
        SUM(CASE WHEN price > 10 AND price <= 100 THEN price * quantity ELSE 0 END) AS total_sales_price_10_to_100,

        SUM(CASE WHEN price > 100 AND price <= 1000 THEN quantity ELSE 0 END) AS quantity_price_100_to_1000,
        SUM(CASE WHEN price > 100 AND price <= 1000 THEN price * quantity ELSE 0 END) AS total_sales_price_100_to_1000,

        SUM(CASE WHEN price > 1000 AND price <= 10000 THEN quantity ELSE 0 END) AS quantity_price_1000_to_10000,
        SUM(CASE WHEN price > 1000 AND price <= 10000 THEN price * quantity ELSE 0 END) AS total_sales_price_1000_to_10000,

        SUM(CASE WHEN price > 10000 THEN quantity ELSE 0 END) AS quantity_price_above_10000,
        SUM(CASE WHEN price > 10000 THEN price * quantity ELSE 0 END) AS total_sales_price_above_10000
    FROM order_products_data;
";
$result = $conn->query($sql);

// Skontrolujeme, či sú údaje dostupné
if ($result->num_rows > 0) {
    // Získanie údajov z prvého riadku
    $row = $result->fetch_assoc();
//******************************************** Výpis údajov do tabuľky ********************************************************//

    echo "<table border='1'>";
    echo "<tr>
            <th>Cenová kategória</th>
            <th>Počet kusov</th>
            <th>Celkový predaj (v €)</th>
          </tr>";
    
    echo "<tr>
            <td>0 €</td>
            <td>{$row['quantity_price_0']}</td>
            <td>{$row['total_sales_price_0']}</td>
          </tr>";
    echo "<tr>
            <td>0 - 1 €</td>
            <td>{$row['quantity_price_0_to_1']}</td>
            <td>{$row['total_sales_price_0_to_1']}</td>
          </tr>";
    echo "<tr>
            <td>1 - 10 €</td>
            <td>{$row['quantity_price_1_to_10']}</td>
            <td>{$row['total_sales_price_1_to_10']}</td>
          </tr>";
    echo "<tr>
            <td>10 - 100 €</td>
            <td>{$row['quantity_price_10_to_100']}</td>
            <td>{$row['total_sales_price_10_to_100']}</td>
          </tr>";
    echo "<tr>
            <td>100 - 1000 €</td>
            <td>{$row['quantity_price_100_to_1000']}</td>
            <td>{$row['total_sales_price_100_to_1000']}</td>
          </tr>";
    echo "<tr>
            <td>1000 - 10000 €</td>
            <td>{$row['quantity_price_1000_to_10000']}</td>
            <td>{$row['total_sales_price_1000_to_10000']}</td>
          </tr>";
    echo "<tr>
            <td>nad 10000 €</td>
            <td>{$row['quantity_price_above_10000']}</td>
            <td>{$row['total_sales_price_above_10000']}</td>
          </tr>";
    echo "</table>";
//************************************************** ukladanie údajov ********************************************************//
    // Otvorenie CSV súboru na zápis
    $file = fopen('output.csv', 'w');
    
    // Zápis hlavičky do CSV
    fputcsv($file, ['Cenová kategória', 'Počet kusov', 'Celkový predaj (v €)']);
    
    // Výpis údajov do CSV
    fputcsv($file, ['0 €', $row['quantity_price_0'], $row['total_sales_price_0']]);
    fputcsv($file, ['0 - 1 €', $row['quantity_price_0_to_1'], $row['total_sales_price_0_to_1']]);
    fputcsv($file, ['1 - 10 €', $row['quantity_price_1_to_10'], $row['total_sales_price_1_to_10']]);
    fputcsv($file, ['10 - 100 €', $row['quantity_price_10_to_100'], $row['total_sales_price_10_to_100']]);
    fputcsv($file, ['100 - 1000 €', $row['quantity_price_100_to_1000'], $row['total_sales_price_100_to_1000']]);
    fputcsv($file, ['1000 - 10000 €', $row['quantity_price_1000_to_10000'], $row['total_sales_price_1000_to_10000']]);
    fputcsv($file, ['nad 10000 €', $row['quantity_price_above_10000'], $row['total_sales_price_above_10000']]);
    
    // Zatvorenie súboru
    fclose($file);

    echo "Údaje boli úspešne uložené do súboru 'output.csv'.";
} else {
    echo "Žiadne údaje na zobrazenie.";
}

// Zatvorenie pripojenia
$conn->close();
?>

</body>
</html>
