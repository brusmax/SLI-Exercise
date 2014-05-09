<?php
/*
* SLI - Exercise 
* Getting info product from external page and store them in local for usage.
* @author: Enrique Rodriguez <rquique@gmail.com>
* Date: 9 May 2014
*/


//Getting post from angular.js
$postdata = file_get_contents("php://input");
$post = json_decode($postdata);

// Defining the basic cURL function
function getHtml($url) {
    // Assigning cURL options to an array
    $options = Array(
        CURLOPT_RETURNTRANSFER => TRUE,  // Setting cURL's option to return the webpage data
        CURLOPT_FOLLOWLOCATION => TRUE,  // Setting cURL to follow 'location' HTTP headers
        CURLOPT_AUTOREFERER => TRUE, // Automatically set the referer where following 'location' HTTP headers
        CURLOPT_CONNECTTIMEOUT => 120,   // Setting the amount of time (in seconds) before the request times out
        CURLOPT_TIMEOUT => 120,  // Setting the maximum amount of time for cURL to execute queries
        CURLOPT_MAXREDIRS => 10, // Setting the maximum number of redirections to follow
        CURLOPT_USERAGENT => "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1a2pre) Gecko/2008073000 Shredder/3.0a2pre ThunderBrowse/3.2.1.8",  // Setting the useragent
        CURLOPT_URL => $url, // Setting cURL's URL option with the $url variable passed into the function
    );
     
    $ch = curl_init();  // Initialising cURL 
    curl_setopt_array($ch, $options);   // Setting cURL's options using the previously assigned array data in $options
    $data = curl_exec($ch); // Executing the cURL request and assigning the returned data to the $data variable
    curl_close($ch);    // Closing cURL 
    return $data;   // Returning the data from the function 
}

// Defining the basic scraping function
function scrapeBetween($data, $start, $end){
    $data = stristr($data, $start); // Stripping all data from before $start
    $data = substr($data, strlen($start));  // Stripping $start
    $stop = stripos($data, $end);   // Getting the position of the $end of the data to scrape
    $data = substr($data, 0, $stop);    // Stripping all data from after and including the $end of the data to scrape
    return $data;   // Returning the scraped data from the function
}

//remove unnecessary from html
function cleanHtml($output){
    $output = str_replace(array("\r\n", "\r"), "\n", $output);
    $lines = explode("\n", $output);
    $new_lines = array();

    foreach ($lines as $i => $line) {
        if(!empty($line))
            $new_lines[] = trim($line);
    }
    return  implode($new_lines);
}

//scrap into html to get info items
function getItem($content, $item){
    switch ($item) {
        case 'title':
            $item_html = scrapeBetween($content, "<div class=\"product-name\">", "</div>");
            return strip_tags($item_html);
            break;
        case 'sku':
            $item_html = scrapeBetween($content, "<div class=\"sku\">", "</div>");
            return strip_tags($item_html);
            break;
        case 'price':
            $item_html = scrapeBetween($content, "<div class=\"price-box\">", "</span>");
            $price = strip_tags($item_html);
            return str_replace('$', '', $price);
            break;
        case 'brand':
            $item_html = scrapeBetween($content, "<div class=\"product-name\">", "</div>");
            $title_array = strip_tags($item_html);
            $branch = explode(' ',trim($title_array));
            return $branch[0];
            break;
        case 'part_numbers':
            //check table id product-attribute-specs-table
            //@TODO

            $item_html = scrapeBetween($content, "<table class=\"data-table\" id=\"product-attribute-specs-table\">", "</table>");
            //echo "print table:";
            //return strip_tags($item_html);
            $contents = "<table class=\"data-table\" id=\"product-attribute-specs-table\">$item_html</table>";
            $DOM = new DOMDocument;
            $DOM->loadHTML($contents);

            $items = $DOM->getElementsByTagName('tr');
            $tecnical_info = array();


            function tdrows($elements)
            {
                $str = "";
                $counter = 0;
                foreach ($elements as $element)
                {
                    $counter++;
                    if($counter == 3)
                        return $element->nodeValue;
                }
                //return $str;
            }
            $manufacturer = false;
            $counter = 0;
            foreach ($items as $node)
            {
                $counter++;
                if($counter == 2) //Manufacturer
                    $tecnical_info['manufacturer'] = tdrows($node->childNodes);
                if($counter == 3) //Model number
                    $tecnical_info['model_number'] = tdrows($node->childNodes);
            }

            return $tecnical_info;
                        

            break;
        case 'short_description':
            $item_html = scrapeBetween($content, "<div class=\"short-description\">", "</div>");
            return strip_tags($item_html);
            break;
        case 'category':
            //@TODO - this only can be saved when the scrip is automated
            return false;
            break;
        case 'img':
            $item_html = scrapeBetween($content, "<p class=\"product-image\">", "</p>");
            preg_match('/(src=["\'](.*?)["\'])/', $item_html, $match); 
            $split = preg_split('/["\']/', $match[0]); 
            $src = $split[1]; 
            return $src;
            break;
        default:
        default:
            return false;
            break;
    }

}
//$scraped_page = getHtml("http://www.etronics.com/idance-blue300r-bluetooth-headphones-red.html");    // Downloading IMDB home page to variable $scraped_page    
$scraped_page = getHtml($post->url);    // Downloading IMDB home page to variable $scraped_page    

$title = getItem($scraped_page, 'title');
//no title means different page.
if(!$title){
    echo "bad url";
    die();
}

//setting vars
$price = getItem($scraped_page, 'price');
$short_description = getItem($scraped_page, 'short_description');
$brand = getItem($scraped_page, 'brand');
$sku = getItem($scraped_page, 'sku');
$part_numbers = getItem($scraped_page, 'part_numbers');
$category = getItem($scraped_page, 'category');
$img = getItem($scraped_page, 'img');


// json file name
$json_name = "products.json";
//open and get info
$products = file_get_contents($json_name);
$data = json_decode($products);
//set data
$index = count($data);
$data[$index]->url = $post->url;
$data[$index]->title = cleanHtml($title);
$data[$index]->price = cleanHtml($price);
$data[$index]->short_description = cleanHtml($short_description);
$data[$index]->brand = cleanHtml($brand);
$data[$index]->part_numbers = $part_numbers;
$data[$index]->sku = cleanHtml($sku);
$data[$index]->category = $category;
$data[$index]->img = cleanHtml($img);
$data[$index]->html = cleanHtml($scraped_page);


//set output
$output  = "Title: ".cleanHtml($title);
$output .= "Price: ".cleanHtml($price);
$output .= "brand: $brand";
$output .= "Manufacturer: ".$part_numbers['manufacturer'];
$output .= "Model Number: ".$part_numbers['model_number'];
$output .= "Short Description:".cleanHtml($short_description);

// Save data into json - ** Permission required
file_put_contents($json_name, json_encode($data));
echo json_encode($output);
?>