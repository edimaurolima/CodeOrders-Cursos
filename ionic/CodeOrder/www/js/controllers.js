angular.module('starter.controllers', [])

    .controller('LoginCtrl', ['$scope', '$http', '$state', 'OAuth', 'OAuthToken', '$localStorage',
        function($scope, $http, $state, OAuth, OAuthToken, $localStorage){
            console.log($localStorage.setObject('oauth', {
<<<<<<< HEAD
                 access_token: "sdfdsf",
=======
                access_token: "sdfdsf",
>>>>>>> c59131adbe8fe963b248788d9c2326e4da539896
                refresh_token: "sdfdsf"
            }));
            console.log($localStorage.getObject('oauth'));
            console.log($localStorage.getObject('label_2'));
            $scope.login = function(data){
                OAuth.getAccessToken(data).then(function(){
                    console.log(window.localStorage['label']);
                    $state.go('tabs.orders');
                }, function(data){
                    $scope.error_login = "Usuário ou senha inválidos";
<<<<<<< HEAD
                    //$scope.error_login = data;
=======
>>>>>>> c59131adbe8fe963b248788d9c2326e4da539896
                });
            }
        }
    ])

    .controller('OrdersCtrl', ['$scope', '$http', '$state',
        function ($scope, $http, $state){

            $scope.getOrders = function(){
                $http.get('http://localhost:8888/orders').then(
                    function (data){
                        $scope.orders = data.data._embedded.orders;
                        console.log($scope.orders);
                    })
            };

            $scope.show = function(order){
                $state.go('tabs.show', {id: order.id})
            };

            $scope.doRefresh = function(){
                $scope.getOrders();
                $scope.$broadcast('scroll.refreshComplete');
            };

            $scope.getOrders();

        }
    ])

    .controller('OrderShowCtrl', ['$scope', '$http', '$stateParams',
        function($scope, $http, $stateParams){
            $scope.getOrder = function(){

                $http.get('http://localhost:8888/orders/' + $stateParams.id).then(
                    function(data){
                        $scope.order = data.data._embedded.items;
                        $scope.order2 = data.data._embedded.items;
                    }
                )
            }

            $scope.getOrder();
        }

    ])

    .controller('LogoutCtrl', ['$scope', 'logout', '$state',
        function($scope, logout, $state){

            $scope.logout = function () {
                logout.logout();
                $state.go('login');


            }
        }

    ])

    .controller('OrderNewCtrl', ['$scope', '$http', '$state',
        function($scope, $http, $state){
<<<<<<< HEAD
            $scope.clientes= [];
=======
            $scope.clients= [];
>>>>>>> c59131adbe8fe963b248788d9c2326e4da539896
            $scope.ptypes= [];
            $scope.products= [];
            $scope.statusList= ["Pendente", "Processando", "Etregue"];

            $scope.resetOrder= function () {
                $scope.order = {
                    client_id : '',
                    ptype_id : '',
                    item : [],
                }
            }

            $scope.getClients = function () {
                $http.get('http://localhost:8888/clients').then(
                    function (data) {
                        $scope.clients = data.data._embedded.clients;
                    }
                )
            }

            $scope.getPtypes = function () {
                $http.get('http://localhost:8888/ptypes').then(
                    function (data) {
                        $scope.ptypes = data.data._embedded.ptypes;
                    }
                )
            }

            $scope.getProducts = function () {
                $http.get('http://localhost:8888/products').then(
                    function (data) {
                        $scope.products = data.data._embedded.products;
                    },
                    function (data) {
                        $scope.erro = data.data;
                    }
                )
            }

            $scope.setPrice = function (index) {
                var product_id = $scope.order.item[index].product_id;
                for(var i in $scope.products){
                    if($scope.products.hasOwnProperty(i) && $scope.products[i].id == product_id){
                        $scope.order.item[index].price = $scope.products[i].price;
                        break;
                    }
                }
                $scope.calculateTotalRow(index);
            };

            $scope.addItem = function () {
                $scope.order.item.push({
                    product_id: '', quantity: '', price: 0, total: 0
                });
            };

            $scope.calculateTotalRow = function (index) {
                $scope.order.item[index].total = $scope.order.item[index].quantity * $scope.order.item[index].price;
                calculateTotal();
            }

            calculateTotal = function () {
                $scope.order.total = 0;
                for(var i in $scope.order.item){
                    if ($scope.order.item.hasOwnProperty(i)){
                        $scope.order.item.total += $scope.order.item[i].total;
                    }
                }
            }

            $scope.save = function () {
                $http.post('http://localhost:8888/orders', $scope.order).then(
                    function (data) {
                        $scope.resetOrder();
                        $state.go('tabs.orders');
                    },
                    function (data) {
                        $scope.erro = data.data;
                        $scope.erro = 'erro ao inserir order e itens'
                    }
                )
            }

            $scope.resetOrder();
            $scope.getClients();
            $scope.getPtypes();
            $scope.getProducts();
        }
    ])

    .controller('RefreshModalCtrl', ['$rootScope','$scope','OAuth', 'authService', '$timeout', '$state', 'OAuthToken', 'logout',

        function($rootScope, $scope, OAuth, authService, $timeout, $state, OAuthToken, logout) {

            function destroyModal() {
                if ($rootScope.modal){
                    $rootScope.modal.hide();
                    $rootScope.modal = false;
                }
            }
            $scope.$on('event:auth-loginConfirmed', function() {
                destroyModal();
            });

            $scope.$on('event:auth-loginCancelled', function() {
                destroyModal();
                logout.logout();
            });

            $scope.$on('$stateChangeStart',
                function(event, toState, toParams, fromState, fromParams){
                    if($rootScope.modal){
                        authService.loginCancelled();
                        event.preventDefault();
                        $state.go('login');
                    }
                });

            OAuth.getRefreshToken().then(function(){
                $timeout(function(){
                    authService.loginConfirmed();
                },3000);
            }, function(){
                authService.loginCancelled();
                $state.go('login');
            })
        }])