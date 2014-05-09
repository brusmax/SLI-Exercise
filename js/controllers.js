/*
* SLI - Exercise 
* Getting info product from external page and store them in local for usage.
* @author: Enrique Rodriguez <rquique@gmail.com>
* Date: 9 May 2014
*/

//init vars
var products = [];
var url = 'functions.php';
var json = 'products.json';
var message = '';
var loading = 'hidden';

//update products scope
var getJson = function($scope, $http){
    $http.get($scope.json).then(function(response) {
            console.log(response.data);
            $scope.products = response.data;
            $scope.total = $scope.products.length;
          });   
}

//search if product already exists on products scope
var searchIntoProducts = function($scope){
    for (var i=0 ; i < $scope.products.length ; i++)
    {
        if ($scope.products[i]["url"] == $scope.keywords) {
            return $scope.products[i]; //found
        }
    }
}

//Search controller
function SearchCtrl($scope, $http) {
    $scope.url = url; 
    $scope.json = json;
    $scope.message = message;
    getJson($scope,$http);
    $scope.loading = loading;


    //search click
    $scope.search = function() {

        $scope.loading = 'show';
        
        //search localy before scrap page
        var product_search = searchIntoProducts($scope);
        //validate url
        if($scope.keywords == undefined) { 
            $scope.message_status = 'danger';
            $scope.message = 'Please set the url';
            $scope.loading = 'hidden';
        }else if(product_search){
            $scope.message_status = 'danger';
            $scope.message = 'This product is already saved';
            $scope.loading = 'hidden';
        } else{
            $scope.message_status = 'success';
            $scope.message = 'Page found';
            //search
            $http.post($scope.url, { url: $scope.keywords}).
            success(function(data, status) {
                if(data == 'bad url'){
                    $scope.message_status = 'danger';
                    $scope.message = 'The script does not find a product, try with a correct one';
                    $scope.loading = 'hidden';
                }else{
                    $scope.status = status;
                    $scope.data = data;
                    $scope.result = data; // affiche le résutlat dans l'élément <pre>    
                    $scope.loading = 'hidden';
                    getJson($scope,$http); //update list
                }
            })
            .
            error(function(data, status) {
                $scope.data = data || "Request failed";
                $scope.status = status;
                $scope.loading = 'hidden';
            });

        }
    };

    //clear click
    $scope.clear = function() {
        $scope.keywords = '';
        $scope.message_status = 'info';
        $scope.result = '';
        $scope.message = 'Insert a new url';
    }
        
}