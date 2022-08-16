<?php
// $db = mysqli_connect("localhost", "root", "asd12D", "scrap");

// // Check connection
// if ($db === false) {
//   die("ERROR: Could not connect. " . mysqli_connect_error());
// } 
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = 'asd12D';
$dbname = 'scrap';


$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

if ($mysqli->connect_errno) {
  printf("Connect failed: %s<br />", $mysqli->connect_error);
  exit();
}
// printf('Connected successfully.<br />');

$sql = "SELECT * FROM cron ORDER BY cron_id DESC LIMIT 1 ";

$result = $mysqli->query($sql);
$lastCrawled = 0;
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo $lastCrawled = $row["crowled_till"];
  }
}
// else {
//   printf('No record found.<br />');
// }
mysqli_free_result($result);


$crawledLinks = [];
$recordsToFetch = 2;
$limit = $lastCrawled == 0 ? $recordsToFetch : ($lastCrawled . "," . $recordsToFetch);

echo $sql = "SELECT * FROM tbl_contractaddress ORDER BY id ASC LIMIT " . $limit;

$result = $mysqli->query($sql);

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo $token = $row["contractAddress"];
    //   }
    //   echo $lastCrawled += $recordsToFetch;
    // }

    // $token = "0x1610bc33319e9398de5f57B33a5b184c806aD217"; //"0x0e09fabb73bd3ade0a17ecc321fd13a19e81ce82";
    $service_url = 'https://bscscan.com/token/' . $token;


    $curl = curl_init($service_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $htmlString = curl_exec($curl);
    curl_close($curl);
    if (!empty($htmlString)) {
      // echo "<pre>";
      // $htmlString = file_get_contents('html.html');
      // print_r($htmlString);

      $dom = new DOMDocument();
      // libxml_use_internal_errors(true);
      @$dom->loadHTML($htmlString);
      // libxml_use_internal_errors(false);

      $finder = new DomXPath($dom);

      $total_supply = $holders = $transactions = "NULL";

      //TOTAL SUPPLY
      $nodes = $finder->query('*//div[@id="ContentPlaceHolder1_divSummary"]//*[contains(@class, "card-body")]//div[contains(@class, "row align-items-center")]');
      if (!is_null($nodes[0])) {

        $total_supply = "NULL";
        if (!empty($nodes[0]->childNodes[3]->nodeValue)) {

          $total_supply = $nodes[0]->childNodes[3]->nodeValue;
          $total_supply = explode(" ", $total_supply)[0];
          echo $total_supply = "'" . str_ireplace(",", "", trim($total_supply)) . "'";
        }
      }


      //Holders

      $nodes = $finder->query('*//div[@id="ContentPlaceHolder1_tr_tokenHolders"]//div[contains(@class, "mr-3")]');
      // echo $holders = isset($nodes->item(0)->nodeValue) ? $nodes->item(0)->nodeValue : "NULL";
      if (isset($nodes->item(0)->nodeValue)) {
        $holders = $nodes->item(0)->nodeValue;
        $holders = explode(" ", $holders)[0];
        echo $holders = "'" . str_ireplace(",", "", trim($holders)) . "'";
      }



      // Transactions
      $nodes = $finder->query('*//span[@id="totaltxns"]');
      // echo $transactions = isset($nodes[0]->nodeValue) ? $nodes[0]->nodeValue : "NULL";
      if (isset($nodes[0]->nodeValue)) {
        $transactions = $nodes[0]->nodeValue;
        $transactions = explode(" ", $transactions)[0];
        echo $transactions = "'" . str_ireplace(",", "", trim($transactions)) . "'";
      }
      // echo $transactions = ($nodes->item(0)->nodeValue);






      // $nodes = $finder->query("//*[contains(concat(' ', normalize-space(@id), ' '), 'ContentPlaceHolder1_divSummary')]");
      // $classname="my-class";
      // $nodes = $finder->query("//*[contains(@class, '$classname')]");
      $nodes = $finder->query('*//div[@id="ContentPlaceHolder1_divSummary"]//*[contains(@class, "card-body")]//li/a');
      // $nodes = $finder->query("//*[contains(@id, 'ContentPlaceHolder1_divSummary')]//*[@class='row']");
      // print_r($nodes); echo "---------------<br>";
      // $titles = $xpath->evaluate('//ol[@class="row"]//li//article//h3/a');
      // $titles = $xpath->evaluate('//[@id="ContentPlaceHolder1_divSummary"]//[@class="card"]//[@class="card-body"]');
      // $elements =  $xpath->query("*/div[@id='ContentPlaceHolder1_divSummary']");
      // print_r($elements);
      $links = array(

        'contractAddress' => "'" . $token . "'",
        'tokenName' => "NULL",
        'symbol' => "NULL",
        'divisor' => "NULL",
        'tokenType' => "NULL",
        'totalSupply' => $total_supply,
        'blueCheckmark' => "NULL",
        'description' => "NULL",
        'website' => "NULL",
        'email' => "NULL",
        'blog' => "NULL",
        'reddit' => "NULL",
        'slack' => "NULL",
        'facebook' => "NULL",
        'twitter' => "NULL",
        'bitcointalk' => "NULL",
        'github' => "NULL",
        'telegram' => "NULL",
        'wechat' => "NULL",
        'linkedin' => "NULL",
        'discord' => "NULL",
        'whitepaper' => "NULL",
        'tokenPriceUSD' => "NULL",
        'twitter_count' => "NULL",
        'fb_count' => "NULL",
        'telegram_count' => "NULL",
        'liquidity' => "NULL",
        'holder_name' => "NULL",
        'holders' => $holders,
        'transactions' => $transactions,
        'locked_count' => "NULL",
        'capital_count' => "NULL"
      );
      // https://api.telegram.org/bot5534796268:AAEQ4I6sczFa8TUmHidBAw7UUNjrnDgNt6Y/getChatMembersCount?chat_id=@VenusProtocol
      if (!is_null($nodes)) {
        foreach ($nodes as $k => $element) {
          echo "<br/>"; //[". $element->nodeName. "].$k";

          if ($k == 0)
            $links["email"] = "'" . ltrim('Email: ', $element->getAttribute('data-original-title')) . "'";
          else {
            $crawledLinks[] = $element->getAttribute('href');
            if (strpos($element->getAttribute('href'), "t.me") !== false) {
              $links["telegram"] = "'" . $element->getAttribute('href') . "'";
            }

            echo $element->getAttribute('href');
          }
        }
      }


      if (count($crawledLinks) > 0)
        foreach ($links as $k => $link) {
          foreach ($crawledLinks as $crl_link) {
            if (strpos($crl_link, $k) !== false) {
              $links[$k] = "'" . $crl_link . "'";
            }
          }
        }
      // print_r($links);
      $tgrm_name = null;
      if ($links["telegram"] != "NULL") {
        // $tgrm_name = ltrim("https://t.me/", $links["telegram"]);
        $tgrm_name = str_ireplace("'https://t.me/", "", $links["telegram"]);
        echo $tgrm_name = rtrim($tgrm_name, "'");
      }

      if ($tgrm_name != "") {
        echo $telegram_api_url = "https://api.telegram.org/bot5534796268:AAEQ4I6sczFa8TUmHidBAw7UUNjrnDgNt6Y/getChatMembersCount?chat_id=@" . $tgrm_name;

        $curl = curl_init($telegram_api_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $curl_response = curl_exec($curl);
        $data = json_decode($curl_response, true);

        if (!empty($data['result']))
          $links["telegram_count"] = $data['result'];

        curl_close($curl);
      }

      // Attempt insert query execution
      echo $sql = "INSERT INTO tbl_user_data (" . implode(",", array_keys($links)) . ") VALUES (" . implode(",", array_values($links)) . ")";

      if ($mysqli->query($sql)) {
        echo "Records inserted successfully.";
      } else {
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($db);
      }
    }
    
  }
  echo $lastCrawled += $recordsToFetch;
  // if($lastCrawled > 0) {
  $sql = "DELETE FROM cron";
  $mysqli->query($sql);

  $sql = "INSERT INTO cron (crowled_till) VALUES (" . $lastCrawled . ")";

  if ($mysqli->query($sql)) {
    echo "Records inserted successfully.";
  } else {
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($db);
  }
  // }


}
else { // IF NO REcord fround from tokens, means all done so start again
  $sql = "DELETE FROM cron";
  $mysqli->query($sql);

  $sql = "DELETE FROM tbl_user_data";
  $mysqli->query($sql);
}
$mysqli->close();
die();
