// Ionic Starter App

// angular.module is a global place for creating, registering and retrieving Angular modules
// 'starter' is the name of this angular module example (also set in a <body> attribute in index.html)
// the 2nd parameter is an array of 'requires'
angular.module('starter', ['ionic', 'starter.controllers','starter.services', 'angular-oauth2', 'http-auth-interceptor'])


    .run(function($ionicPlatform, $rootScope, OAuth, $state, httpBuffer, $ionicModal) {
      $ionicPlatform.ready(function() {
        // Hide the accessory bar by default (remove this to show the accessory bar above the keyboard
        // for form inputs)
        if(window.cordova && window.cordova.plugins.Keyboard) {
          cordova.plugins.Keyboard.hideKeyboardAccessoryBar(true);
        }
        if(window.StatusBar) {
          StatusBar.styleDefault();
        }
      });

      $rootScope.$on('$stateChangeStart',
           function(event, toState, toParams, fromState, fromParams, options){
               if(toState.name != 'login'){
                    if(!OAuth.isAuthenticated()){
                        event.preventDefault();
                        $state.go('login');
                    }
               }
           });
      $rootScope.$on('oauth:error', function (event, data) {
          if(data.rejection.statusText ==  'invalid_grant'){
              return;
          }

          if(data.rejection.status == 401){
              httpBuffer.append(data.rejection.config,data.deffered);
              if(!$rootScope.modal){
                  $ionicModal.fromTemplateUrl("my-modal.html",{
                      scope: $rootScope.$new(),
                      animation: 'slide-in-up'
                  }).then(function (modal) {
                      $rootScope.modal = modal;
                      $rootScope.modal.show();
                  });
              }
              return;
          }

          $state.go('login');
          
      })
    })

    .config(function ($stateProvider, $urlRouterProvider, $ionicConfigProvider, OAuthProvider, OAuthTokenProvider,
                      $httpProvider, $provide){

        $httpProvider.interceptors.push('oauthFixInterceptor');
        $httpProvider.interceptors.splice(0,1);

        OAuthProvider.configure({
            baseUrl: 'http://localhost:8888',
            clientId: 'cliente',
            clientSecret: 'senha', // optional
            grantPath: '/oauth',
            revokePath: '/oauth'
        });

        OAuthTokenProvider.configure({
            name: 'token',
            options: {
                secure: false
            }
        });

        $stateProvider
            .state('tabs', {
              url: '/t',
              abstract: true,
              templateUrl: 'templates/tabs.html'
            })

            .state('tabs.orders', {
              url: '/orders',
              views: {
                'orders-tab': {
                    templateUrl: 'templates/orders.html',
                    controller: 'OrdersCtrl'
                }
              }
            })
            .state('tabs.create', {
                url: '/create',
                views: {
                    'create-tab': {
                        templateUrl: 'templates/create.html',
                        controller: 'OrderNewCtrl'
                    }
                }
            })

            .state('tabs.logout', {
                url: '/logout',
                views: {
                    'logout-tab': {
                        templateUrl: 'templates/logout.html',
                        controller: 'LogoutCtrl'
                    }
                }
            })

            .state('tabs.show', {
                url: '/orders/:id',
                views: {
                    'orders-tab': {
                        templateUrl: 'templates/order-show.html',
                        controller: 'OrderShowCtrl'
                    }
                }
            })

            .state('login', {
                cache: false,
                url: '/login',
                templateUrl: 'templates/login.html',
                controller: 'LoginCtrl'
            })

        $urlRouterProvider.otherwise('/login');
        $provide.decorator('OAuthToken', ['$delegate', '$localStorage',
            function ($delegate, $localStorage) {
                Object.defineProperties($delegate, {
                    setToken: {
                        value: function (data) {
                            return $localStorage.setObject('token',data);
                        },
                        enumerable:true,
                        configurable: true,
                        writable: true
                    },
                    getToken: {
                        value: function () {
                            return $localStorage.getObject('token');
                        },
                        enumerable:true,
                        configurable: true,
                        writable: true
                    },
                    removeToken: {
                        value: function () {
                            $localStorage.setObject('token',null);

                        },
                        enumerable:true,
                        configurable: true,
                        writable: true
                    }
                });
                return $delegate;
            }]);
    })