<?php
/*
Jack::Slide

Списки возможных методов для cURL:
//get price
// $url = "https://online.moysklad.ru/api/remap/1.1/entity/pricelist";
//get currencies
// $url = "https://online.moysklad.ru/api/remap/1.1/entity/currency/";
//get meta data
// $url = "https://online.moysklad.ru/api/remap/1.1/entity/product/metadata/";
*/


//Получаем остатки
$url = "https://online.moysklad.ru/api/remap/1.1/report/stock/all?sort=product&limit=9999";

//открываем курл, логинимся, получаем ответ JSON, преобразовываем в массив
$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_USERPWD, "LOGIN : PASS");
$result = curl_exec($ch);
curl_close($ch);
$respon = json_decode($result, true);

//создаем линк с базой данных
$db = mysqli_connect ("127.0.0.1", "LOGIN", "PASS", "DBNAME") or die (mysqli_error ($db));

//Получаем количество и артикул всех продуктов в магазине, заносим их в массив
$result = mysqli_query($db, "SELECT `quantity` , `sku` FROM `yupe_store_product`", MYSQLI_USE_RESULT);
    while ($row = $result->fetch_assoc()){
        $user_arr[] = $row;
    }


//запускаем проход по базе на основе полученных из МойСклад количеств
for ($i=0; $i < count($user_arr) ; $i++) {
  $artik = $respon['rows'][$i]['code'];//Артикул товара из МойСклад
  $quant = $respon['rows'][$i]['quantity'];//Кол-во товара на МойСклад
  mysqli_query($db, "UPDATE `yupe_store_product` SET `quantity`='".$quant."' WHERE `sku`='".$artik."'");//обновляем все товары
  //есои количество меньше нуля или ноль, ставим нет в наличии
if($user_arr[$i]['quantity'] <= 0){
  mysqli_query($db, "UPDATE `yupe_store_product` SET `in_stock`='0' WHERE `sku`='".$user_arr[$i]['sku']."'");
}
else //иначе если больше 0, ставим в наличии
{
  mysqli_query($db, "UPDATE `yupe_store_product` SET `in_stock`='1' WHERE `sku`='".$user_arr[$i]['sku']."'");
}
}

//Если соединение не закрывто, закрываем соединение
if ($db) {
    mysqli_close($db);
}
  ?>
