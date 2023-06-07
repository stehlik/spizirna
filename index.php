<?php

use Nette\Utils\Floats;

session_start();

$db = new PDO(
    "mysql:host=localhost;dbname=spizirna;charset=utf8",
    "root",
    "",
    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ),
);

$chyby = [];
$chyba = "";
$nazevPotraviny = "";
$jednotka = "";
$mnozstvi = "";
$minimum = "";
$noveMnozstvi ="";
$ulozeno = false;
$id = "";
$dotaz="";
$rozdil ="";
$upozorneni ="";
$zmenitMinimum = "";

// Najdi potravinu ve spíži
if (array_key_exists("vyhledat", $_POST)){
    $nazevPotraviny = $_POST["nazevPotraviny"];
    if ($nazevPotraviny == ""){
        $chyba = "Název potraviny musí být zadán";
    }else{
        $dotaz = $db->prepare("SELECT * FROM potravina WHERE nazev = ?");
        $dotaz->execute([$nazevPotraviny]);
        $vysledek = $dotaz->fetch();
        if ($vysledek == false)
        {
            $minimum = " ";
            $chyba = "Potravina nebyla nalezena";
        }
        else{
            $mnozstvi = $vysledek["mnozstvi"];
            $jednotka = $vysledek["jednotka"];
            $minimum = $vysledek["minimum"];
            $id = $vysledek["id"];
        }
        if($minimum == null){
            $minimum = 0;
        }
    } 
    $_SESSION["mnozstvi"] = $mnozstvi;
    $_SESSION["minimum"] = $minimum;
    $_SESSION["id"] = $id;
    $_SESSION["nazev"] = $nazevPotraviny;
}

//Uprav množství
if (array_key_exists("vlozit", $_POST) && $_SESSION["id"] != ""){
    $zmenitMnozstvi = doubleval($_POST["zmenitMnozstvi"]);
if ($_POST["zmenitMnozstvi"] == ""){
}else{
    $noveMnozstvi = $zmenitMnozstvi + $_SESSION["mnozstvi"];
    $rozdil = $_SESSION["minimum"] - $noveMnozstvi;
    $dotaz = $db->prepare("UPDATE potravina SET mnozstvi = '{$noveMnozstvi}', rozdil = '{$rozdil}' WHERE id = {$_SESSION['id']}");
    $dotaz->execute();
    $_SESSION["id"] = "";
} 
}

//Uprav minimum
if (array_key_exists("navysit", $_POST) && $_SESSION["id"] != "" ){
    $zmenitMinimum = doubleval($_POST["zmenitMinimum"]);
if ($_POST["zmenitMinimum"] == ""){
} else{
    $rozdil = $zmenitMinimum- $_SESSION["mnozstvi"];
    $dotaz = $db->prepare("UPDATE potravina SET minimum = '{$zmenitMinimum}', rozdil = '{$rozdil}' WHERE id = {$_SESSION['id']}");
    $dotaz->execute();
    $_SESSION["id"] = "";
}
}

//Vyhodit potravinu do popelnice
if (isset($_POST['smazat']) && $_SESSION["id"] == ""){
    $chyby["smazat"] = "Není vybrána žádná potravina";
} else if (isset($_POST['smazat']) && $_SESSION["id"] != ""){
    $chyby["otazka"] = "Doopravdy chceš tuto potravinu vyhodit do popelnice?";
}
 if(isset($_POST['nesouhlas']) && $_SESSION["id"] != "") {$chyby["otazka"] = "";
}
else if(isset($_POST['souhlas']) && $_SESSION["id"] != "") {
    
    $dotaz = $db->prepare("DELETE FROM potravina WHERE id = '{$_SESSION['id']}'");
    $dotaz->execute();
    $upozorneni = "Potravina byla vyhozena do popelnice";
    $_SESSION["id"] = "";
    $_SESSION["nazev"] = "";
    
}

// Ulož novou potravinu do spíže
if(array_key_exists("ulozit", $_POST)){
    $nazevNovePotraviny = $_POST["nazevNovePotraviny"];
    $druhNovePotraviny = $_POST["druhNovePotraviny"];
    $mnozstviNovePotraviny = $_POST["mnozstviNovePotraviny"];
    $jednotkaNovePotraviny = $_POST["jednotkaNovePotraviny"];
    $minimumNovePotraviny = $_POST["minimumNovePotraviny"];

    //validace
    if($nazevNovePotraviny == ""){
        $chyby["nazevNovePotraviny"] = "Musí být vyplněno";
    }
    if($druhNovePotraviny == ""){
        $chyby["druhNovePotraviny"] = "Musí být vybráno";
    }
    if($mnozstviNovePotraviny == ""){
        $chyby["mnozstviNovePotraviny"] = "Musí být vyplněno";
    }else if (!is_numeric($mnozstviNovePotraviny) || $mnozstviNovePotraviny < 0)
    {
        $chyby["mnozstviNovePotraviny"] = "Musí být nezáporné číslo";
    }
    if (!is_numeric($minimumNovePotraviny) || $minimumNovePotraviny < 0)
    {
        $chyby["minimumNovePotraviny"] = "Musí být nezáporné číslo";
    }
    if (count($chyby) == 0)
    {
        // vse je ok
        $ulozeno = true;
        $rozdil = $minimumNovePotraviny - $mnozstviNovePotraviny;
        //vložíme potravinu do databáze
        $dotaz = $db->prepare("INSERT INTO potravina SET nazev = ?, druh_id = ?, mnozstvi = ?, jednotka = ?, minimum = ?, rozdil = ?");
        $dotaz->execute([$nazevNovePotraviny, $druhNovePotraviny, $mnozstviNovePotraviny, $jednotkaNovePotraviny, $minimumNovePotraviny, $rozdil]);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spižírna</title>
    <style>
        h2{
           text-decoration: underline; 
        }
        body{
            background-color: lightskyblue;
            font-family: Arial, Helvetica, sans-serif;
        }
        .chyba{
            color: red;
            font-weight: bold;
        }
        .zmena{
            color: green;
            font-weight: bold;
        }
        .container {
            display: flex;
            
        }
        .container > div{
            border-radius: 5px;
            border-style: solid;
            border-color: blue;
            border-width: 5px;
            padding: 5px;
            margin: 10px;
            background-color: lightgoldenrodyellow;
        }
        .nakupniSeznam {
            margin: 10px;
            border-radius: 5px;
            border-style: solid;
            border-color: green;
            border-width: 5px;
            padding: 5px;
            width: 530px;
        }
        .nakupniSeznam > div {
            margin-bottom: 10px;
        }
        .recepty{
            width: 440px;
        }
    </style>
</head>
<body>
<div class="container">
    <div>
        <h2>Najdi potravinu ve spižírně</h2>
        <form method="post">
            <label for="nazevPotraviny">Potravina:</label>
            <input type="text" id="nazevPotraviny" name="nazevPotraviny" value="<?php if (isset($_SESSION["id"])){echo  $_SESSION["nazev"];}else{echo"Název potraviny"; } ?>">
            <button name="vyhledat">Hledej</button>
            <?php
            echo "<span class='chyba'>{$chyba}</span>";
            ?>
        </form>
    
        <table>
            <tr><td>Ve spižírně je:</td> <td width=80px><b><?php echo $mnozstvi." ". $jednotka;?></b></td>        
                <td>
                    <form method="post">
                        <input type="text" name="zmenitMnozstvi"><?php echo " ".$jednotka;?>
                        <button name="vlozit">Vložit nebo odebrat množství</button>
                    </form>
                </td>
            </tr>
            <tr>
                <td>Minimální množství je:</td> <td><b><?php echo $minimum." ". $jednotka;?></b></td>
                <td>
                    <form method="post">
                        <input type="text" name="zmenitMinimum"><?php echo " ".$jednotka;?>
                        <button name="navysit">Nastavit nové minimum</button>
                    </form>
                </td>
            </tr>
        </table> 
        <br>

        <div>
            <form method="post">
                <button name="smazat">Vyhodit potravinu</button>
            </form>
        </div>
            <?php 
                if (array_key_exists("vlozit", $_POST) && $_POST["zmenitMnozstvi"] != ""){
                    echo "<span class='zmena'> Množství bylo změněno";
                }else{
                    echo "";
                }
                if (array_key_exists("navysit", $_POST) && $_POST["zmenitMinimum"] != ""){
                    echo "<span class='zmena'> Minimum bylo změněno </span>";
                }else{
                    echo "";
                }
                
                if (isset($_POST['smazat']) && $_SESSION["id"] != ""){
                    echo "<span class='chyba'>{$chyby['otazka']}</span>";
                    echo "<form method='post'>";
                    echo "<button name='souhlas'>Ano</button>   <button name='nesouhlas'>Ne</button>";
                    echo "</form>";
                }
                if (array_key_exists("souhlas", $_POST)){
                    echo "<span class='zmena'> $upozorneni </span>";
                }
                if (array_key_exists("nesouhlas", $_POST)){
                    echo "{$chyby['otazka']}";
                }
                if (array_key_exists("smazat", $chyby)){
                    echo "<span class='chyba'>{$chyby['smazat']}</span>";
                }
            ?>
    </div>

    <div>
        <h2>Ulož do spižírny novou potravinu</h2>
        <form method="post">
            <label for="nazevNovePotraviny">Název:</label>
            <input type="text" name="nazevNovePotraviny" id="nazevNovePotraviny">
            <?php
            if (array_key_exists("nazevNovePotraviny", $chyby))
                {
                echo "<span class='chyba'>{$chyby['nazevNovePotraviny']}</span>";
                }
            ?>    
            <br>
            <label for="druhNovePotraviny">Druh:</label>
                <select name="druhNovePotraviny" id="druhNovePotraviny">
                    <option value="">Vyber</option>
                    <?php
                        $dotaz = $db->prepare("SELECT id, nazev FROM druh ORDER BY nazev");
                        $dotaz->execute();
                        $druhNovePotraviny = $dotaz->fetchAll();
                        foreach ($druhNovePotraviny as $druh)
                        {
                            echo "<option value='{$druh['id']}'>{$druh['nazev']}</option>";
                        }
                    ?>
                </select>
                <?php
                    if (array_key_exists("druhNovePotraviny", $chyby))
                    {
                    echo "<span class='chyba'>{$chyby['druhNovePotraviny']}</span>";
                    }
                ?>
            <br>
            <label for="mnozstviNovePotraviny">Množství:</label>
            <input type="text" name="mnozstviNovePotraviny" id="mnozstviNovePotraviny">
            <select name="jednotkaNovePotraviny" id="">
                <option value="kg">kg</option>
                <option value="ks">ks</option>
                <option value="l">l</option>
            </select>
            <?php
                if (array_key_exists("mnozstviNovePotraviny", $chyby))
                    {
                    echo "<span class='chyba'>{$chyby['mnozstviNovePotraviny']}</span>";
                    }
            ?> 
            <br>
            
            <label for="minimumNovePotraviny">Minimum:</label>
            <input type="text" name="minimumNovePotraviny" id="minimumNovePotraviny">
            <?php
            if (array_key_exists("minimumNovePotraviny", $chyby))
                {
                echo "<span class='chyba'>{$chyby['minimumNovePotraviny']}</span>";
                }
            ?> 
            <br>
            <br>
            <button name="ulozit">Ulož potravinu</button>
            <?php
            if($ulozeno == true){
                echo "<span class='zmena'>Potravina byla uložena do spižírny</span>";
                }
            ?>

        </form>
    </div>
</div>

<div class="container">
    <div class = "nakupniSeznam">
        <div>
            <h2>Co chybí ve spižírně?</h2>
            <form action="" method="post">
                <input type="submit" name="vypsat" value="Vypsat nákupní seznam">
            </form>
        </div>
        <div >
            <table border=1>
                <tr>
                    <th>Potravina</th>
                    <th>Druh</th>
                    <th>Množství ve spíži</th>
                    <th>Nastavené minimum</th>
                    <th>Potřeba dokoupit</th>
                </tr>
                <?php
                    if(isset($_POST['vypsat'])){
                    $dotaz = $db->prepare("SELECT potravina.id, potravina.nazev, potravina.mnozstvi, potravina.jednotka, potravina.minimum, potravina.rozdil, druh.nazev FROM potravina JOIN druh ON potravina.druh_id = druh.id where rozdil >= 0");
                    $dotaz->execute();
                    $vysledek = $dotaz->fetchAll();
                    //var_dump($vysledek);
                    foreach($vysledek as $vysl){
                    ?>
                    <tr>
                        <td><?php echo $vysl[1] ?></td>
                        <td><?php echo $vysl['nazev'] ?></td>
                        <td><?php echo $vysl['mnozstvi']." " .$vysl['jednotka'] ?></td>
                        <td><?php echo $vysl['minimum']." " .$vysl['jednotka'] ?></td>
                        <td><?php 
                        if ($vysl['rozdil'] == 0){
                            echo "Pozor! Množství této potraviny je na minimu.";
                        }else{
                            echo "{$vysl['rozdil']} {$vysl['jednotka']}";
                        }
                        ?></td>
                    </tr>
                    <?php
                    }
                    }
                    ?>
            </table>
        </div>
    </div>

    <div class="recepty">
        <div>
            <h2>Pojďme uvařit třeba...</h2>
        </select>

            <?php
                $dotaz = $db->prepare("SELECT * FROM recept ORDER BY nazev ASC");
                $dotaz->execute();
                $vysledek = $dotaz->fetchAll();
                echo "<ul>";
                foreach ($vysledek as $recept)
                    {
                        echo "<li><a href='?id={$recept['id']}'>{$recept['nazev']}</a></li>";
                    }
                echo "</ul>";

                if(array_key_exists("id", $_GET)){
                    $idReceptu = $_GET["id"];
                    $dotaz = $db->prepare("SELECT * FROM recept where id = ?");
                    $dotaz->execute([$idReceptu]);
                    $recept = $dotaz->fetch();
                    $dotaz = $db->prepare("SELECT potraviny_v_receptu.id_receptu, potravina.nazev, potravina.mnozstvi, potraviny_v_receptu.mnozstvi, potraviny_v_receptu.jednotka FROM potravina JOIN potraviny_v_receptu ON potraviny_v_receptu.id_potraviny = potravina.id where id_receptu = ?");
                    $dotaz->execute([$idReceptu]);
                    $vysledek = $dotaz->fetchAll();
                    echo "<h3>{$recept['nazev']}</h3>";
                    echo "<table border=1>
                            <tr>
                                <th>Ingredience</th>
                                <th>Množství</th>
                                <th>Poznámka</th>
                            </tr>";
                        foreach($vysledek as $vysl){
                        echo "<tr>
                                <td> 
                                    {$vysl['nazev']} 
                                </td>
                                <td> 
                                    {$vysl['mnozstvi']} {$vysl['jednotka']} 
                                </td>";
                            if ($vysl['2'] < $vysl['mnozstvi']){
                                echo "<td>Pozor! Ve spíži máš jen {$vysl['2']} {$vysl['jednotka']}</td>";
                            }
                        echo "</tr>";
                        }
                    echo "</table>";
                    echo "<p>{$recept['postup']}</p>";
                }
            ?>
        </div>
    </div>
</div>
  
</body>
</html>
