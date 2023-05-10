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
$mnozstvi = null;
$minimum = "";
$noveMnozstvi =null;
$ulozeno = false;
$id = "";
$dotaz="";
$rozdil ="";
$odpoved="";


// Najdi potravinu ve spíži
if (array_key_exists("vyhledat", $_POST)){
    $nazevPotraviny = $_POST["nazevPotraviny"];
    if ($nazevPotraviny == ""){
        $chyba = "Název potraviny musí být zadán";
    }
    else{
        $dotaz = $db->prepare("SELECT * FROM potravina WHERE nazev = ?");
        $dotaz->execute([$nazevPotraviny]);
        $vysledek = $dotaz->fetch();
        //var_dump($vysledek);
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
        //var_dump($id);
    } 
    $_SESSION["mnozstvi"] = $mnozstvi;
    $_SESSION["minimum"] = $minimum;
    $_SESSION["id"] = $id;
}

    //Uprav množství
if (array_key_exists("vlozit", $_POST)){
    $navysitMnozstvi = $_POST["navysitMnozstvi"];
    $noveMnozstvi = $navysitMnozstvi + $_SESSION["mnozstvi"];
    //var_dump($navysitMnozstvi);
    //var_dump($_SESSION["mnozstvi"]);
    //var_dump($noveMnozstvi);
    $rozdil = $_SESSION["minimum"] - $noveMnozstvi;
    //var_dump($rozdil);
    $dotaz = $db->prepare("UPDATE potravina SET mnozstvi = '{$noveMnozstvi}', rozdil = '{$rozdil}' WHERE id = {$_SESSION['id']}");
    $dotaz->execute();
    $_SESSION["id"] = "";
} 

//Uprav minimum
if (array_key_exists("navysit", $_POST)){
    $navysitMinimum = $_POST["navysitMinimum"];
    $rozdil = $navysitMinimum- $_SESSION["mnozstvi"];
    $dotaz = $db->prepare("UPDATE potravina SET minimum = '{$navysitMinimum}', rozdil = '{$rozdil}' WHERE id = {$_SESSION['id']}");
    $dotaz->execute();
    //var_dump($navysitMinimum);var_dump($rozdil);
    $_SESSION["id"] = "";
} 

//Vyhodit potravinu do popelnice
if (isset($_POST['smazat']) && $_SESSION["id"] == ""){
    $chyby["smazat"] = "Není vybrána žádná potravina";
} else if (isset($_POST['smazat']) && $_SESSION["id"] != ""){
    $otazka = 

    $dotaz = $db->prepare("DELETE FROM potravina WHERE id = '{$_SESSION['id']}'");
    $dotaz->execute();
    $odpoved = "Potravina byla vyhozena do popelnice";
    //var_dump($_POST['smazat']);
    //var_dump($_SESSION['id']);
    $_SESSION["id"] = "";
    
}

// Vlož novou potravinu do spíže
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
            border-color: green;
            border-width: 5px;
            padding: 5px;
            margin: 10px;
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
            width: 450px;
        }
    </style>
</head>
<body>
<div class="container">
    <div>
        <h2>Najdi potravinu ve spižírně</h2>
        <form method="post">
            <label for="nazevPotraviny">Potravina:</label>
            <input type="text" name="nazevPotraviny" value="<?php echo $nazevPotraviny ?>">
            <button name="vyhledat">Hledej</button>
            <?php
            echo "<span class='chyba'>{$chyba}</span>";
            ?>
        </form>
    
        <table >
            <tr><td>Ve spižírně je:</td> <td width=100px><?php echo $mnozstvi." ". $jednotka;?></td>        
                <td>
                    <form method="post">
                        <input type="text" name="navysitMnozstvi"><?php echo " ".$jednotka;?>
                        <button name="vlozit">Vložit nebo odebrat množství</button>
                    </form>
                </td>
            </tr>
            <tr>
                <td>Minimální množství je:</td> <td><?php echo $minimum." ". $jednotka;?></td>
                <td>
                    <form method="post">
                        <input type="text" name="navysitMinimum"><?php echo " ".$jednotka;?>
                        <button name="navysit">Nastavit nové minimum</button>
                    </form>
                </td>
            </tr>
        </table> 
        <br>

        <div>
            <form method="post">
                <button name="smazat">Vyhoď potravinu</button>
            </form>
        </div>
            <?php 
                if (array_key_exists("vlozit", $_POST)){
                    echo "<span class='zmena'> Množství bylo změněno </span>";
                }
                if (array_key_exists("navysit", $_POST)){
                    echo "<span class='zmena'> Minimum bylo změněno </span>";
                }
                if (isset($_POST['smazat'])){
                    echo "<span class='zmena'> $odpoved </span>";
                }
                if (array_key_exists("smazat", $chyby)){
                    echo "<span class='chyba'>{$chyby['smazat']}</span>";
                }
            ?>
        

    </div>

    <div>
        <h2>Vlož do spižírny novou potravinu</h2>
        <form method="post">
            <label for="nazevNovePotraviny">Název potraviny:</label>
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
            <button name="ulozit">Vlož potravinu</button>
            <?php
            if($ulozeno == true){
                echo "<span class='zmena'>Potravina byla vložena do spižírny</span>";
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
                <input type="submit" name="vypsat" value="Vypiš nákupní seznam">
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
                            echo "Pozor! Tato potravina je na nastaveném minimu.";
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
            <h2>Pojďme uvařit třeba:</h2>
        <!-- <select name="recept" id="recept" form="recept">
            <option value="">Vyber recept</option>
            <option value="1">Guláš</option>
            <option value="2">Bramborový salát</option>
            <option value="3">Vepřový řízek</option>
            
            <?php /*
                $dotaz = $db->prepare("SELECT id, nazev FROM recept ORDER BY nazev");
                $dotaz->execute();
                $recepty = $dotaz->fetchAll();
                foreach ($recepty as $recept)
                {
                    
                    echo "<option value='{$recept['id']}'>{$recept['nazev']}</option>";
                    //$_SESSION["recept"] = $recept['id'];
                    //$idreceptu = $recept['id'];
                }*/
                
            ?>
        </select>
        
        <form action="" method="post" id="recept">
            <input type="submit" name="vypismi" value="Ukaž recept">
        </form>   
        -->


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
                    //var_dump($recept);
                    
                    //$dotaz = $db->prepare("SELECT potraviny_v_receptu.id_receptu, potravina.nazev FROM potravina JOIN potraviny_v_receptu ON  potraviny_v_receptu.id_potraviny = potravina.id");
                    //$dotaz->execute();

                    $dotaz = $db->prepare("SELECT potraviny_v_receptu.id_receptu, potravina.nazev, potravina.mnozstvi, potraviny_v_receptu.mnozstvi, potraviny_v_receptu.jednotka FROM potravina JOIN potraviny_v_receptu ON potraviny_v_receptu.id_potraviny = potravina.id where id_receptu = ?");
                    $dotaz->execute([$idReceptu]);
                    $vysledek = $dotaz->fetchAll();
                    //var_dump($vysledek);

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