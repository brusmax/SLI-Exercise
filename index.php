<!DOCTYPE html>
<html ng-app>
<head>
<title>SLI-Exercise</title>
    <meta name="author" content="Enrique Rodriguez" email="rquique@gmail.com">
    <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.1.1/css/bootstrap.min.css" type="text/css" />
    <script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.0.2/angular.min.js"></script>
    <script src="js/controllers.js"></script>
</head>

<body>
    <div class="panel panel-default" ng-controller="SearchCtrl">
      
      <div class="panel-heading">Save single product page ( <b>from: <a href="http://www.etronics.com" target="_blank">www.etronics.com</a></b>)</div>
      <div class="panel-body">
        <div class="input-group">

            <form>
                <input type="url" required ng-model="keywords" class="form-control" placeholder="Example: http://www.etronics.com/idance-blue300r-bluetooth-headphones-red.html">
            </form>

          <span class="input-group-btn">
            <button class="btn btn-default" type="button" ng-click="search()">Save Product</button>
          </span>

        </div><!-- end input-group -->
        <br>
        <div class="panel panel-default">
          <div class="panel-body">
            <img src="img/loading.gif" class="{{loading}}" />
            <div class="alert alert-{{message_status}}">{{message}}</div>
            <span class="label label-primary">Output: </span>
            <div class="well" ng-model="result">{{result}}</div>
            <button type="button" class="btn btn-info pull-right" ng-click="clear()">Clear</button>
          </div> <!--  panel-body -->
        </div><!--  panel -->

        <div class="panel panel-default">
           <div class="panel-heading">
            <h3 class="panel-title">Products saved ({{total}})</h3>
          </div>
          <div class="panel-body">
              <div class="form-group">
               <input type="text" class="form-control" ng-model="product.title" placeholder="Search Product">
              </div> <!--  end form-group -->

              <div class="media list-group-item" ng-repeat="product in products.slice().reverse() | filter:product">
                  <a class="pull-left" href="{{product.url}}" target="_blank">
                    <img class="media-object" ng-src="{{product.img}}" alt="{{prodcut.title}}" width="100">
                  </a>
                  <div class="media-body">
                    <h4 class="media-heading">{{product.title}}</h4>
                    <p>{{product.short_description}}</p>
                    <p><span class="label label-primary">{{product.price | currency}}</span> <a ng-href="{{product.url}}" target="_blank">View</a></p>
                  </div><!--  end media-body --> 
                </div> <!--  end list-group-item -->


          </div> <!-- end  panel-body -->
        </div> <!--  end panel-default -->
      </div><!-- end panel-body-->
    </div><!--  end SearchCtrl-->
</body>

</html>