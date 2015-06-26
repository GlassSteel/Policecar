<!DOCTYPE html>
<html>
<head>
	<title>Police Car!</title>
</head>
<body>
	<div id="pc" ng-controller="PC_Controller as main_ctrl">
		<label>This:</label><input type="text" ng-model="main_ctrl.resource.one" />
		<label>That:</label><input type="text" ng-model="main_ctrl.resource.two" />
		<button type="button" ng-click="main_ctrl.submitFail()">Submit Fail</button>
		<button type="button" ng-click="main_ctrl.submitOK()">Submit OK</button>

		<pre>Resource = {{ main_ctrl.resource | json }}</pre>
		<pre>Response = {{ main_ctrl.response | json }}</pre>
	</div>

	<script type="text/javascript" src="/bower_components/angular/angular.min.js"></script>
	<script type="text/javascript" src="/bower_components/angular-local-storage/dist/angular-local-storage.min.js"></script>

	<script>
		'use strict';
		function PC_Controller($http, $scope, $window, localStorageService){
		    var vm = this;
		    vm.heya = 'Heya !';
		    vm.response = {};
		    vm.resource = localStorageService.get('draft') || {};

		    $scope.$on('responseError',function(event, data){
		        localStorageService.set('draft', vm.resource);
				$window.location.href = 'http://auth.dev';
		    });

		    vm.submitFail = function(){
		    	vm.response = {};
		    	vm.iframe_src = false;

				var request = $http({
					method: "post",
	                headers: {
	                	'Content-type' : 'application/json',
	                },
	                url: '/auth.php',
					data: vm.resource,
	            }).
	            success(function(response, status, headers, config) {
	            	vm.response.response = response;
	            	vm.response.status = status;
	            }).
	            error(function(data, status, headers, config) {
	            	vm.response.response = response;
	            	vm.response.status = status;
	            });
	        }//submit()

	        vm.submitOK = function(){
		    	vm.response = {};
		    	vm.iframe_src = false;

				var request = $http({
					method: "post",
	                headers: {
	                	'Content-type' : 'application/json',
	                },
	                url: '/ok.php',
					data: vm.resource,
	            }).
	            success(function(response, status, headers, config) {
	            	vm.response.response = response;
	            	vm.response.status = status;
	            	localStorageService.remove('draft');
	            }).
	            error(function(data, status, headers, config) {
	            	vm.response.response = response;
	            	vm.response.status = status;
	            });
	        }//submit()
		
		}//PC_Controller

		var pc = angular.module('pc', ['LocalStorageModule']);

		pc.controller('PC_Controller',PC_Controller);

		pc.config(function($httpProvider){
		  	$httpProvider.interceptors.push(function($q, $injector) {
		  		return {
		      		request: function(request) {
		        		return request;
		      		},
		      		responseError: function(rejection) {
      			  		if (rejection.status === 302) {
      			  			var rootScope = rootScope || $injector.get('$rootScope');
							rootScope.$broadcast('responseError',{
      			  			    message: 'Auth Timedout',
      			  			}); 
						}
						// If not a 401, do nothing with this error.
		         		return $q.reject(rejection);
		      		}
		    	};
		  	});
		});

		pc.config(function (localStorageServiceProvider) {
		  	localStorageServiceProvider.setPrefix('glasteel');
		});

		angular.element(document).ready(function() {
		    var el = document.getElementById('pc');
		    angular.bootstrap(el, ['pc']);
		});
	</script>
</body>
</html>