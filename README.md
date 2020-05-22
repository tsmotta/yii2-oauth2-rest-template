# REST API application with OAuth2 server on [Yii2](https://github.com/yiisoft/yii2)

This is a Yii2 Rest App template configured with OAuth2 server (using https://github.com/hosannahighertech/yii2-oauth2-server) with JWT support. Resolved all problems on adaptation OAuth2 server extension, built directory structure with versions (as recommended in [official guide](http://www.yiiframework.com/doc-2.0/guide-rest-versioning.html)), added some ready-to-use features for faster development. 

Forked from https://github.com/ikaras/yii2-oauth2-rest-template with some simplifications

You can use this template as a starting poing to create API side of your service.

## Installation

Clone or fork the repo

## Configurations

1. Use the api folder content so you don't need to create vhost or something like that. Works for non dedicated hosting servers
2. Change connection to your db in `api/config/db.php`
3. Run migrations
```
php api/yii migrate --migrationPath=@yii/rbac/migrations --interactive=0 \
php api/yii migrate --migrationPath=@vendor/hosannahighertech/yii2-oauth2-server/migrations --interactive=0 \
php api/yii migrate --interactive=0 \
php api/yii migrate --interactive=0 \
```

## Modifications

1. Change line 81 of yii\base\ActionFilter.php to add test before $this->owner->on:

```
if ($this->owner) {
    $this->owner->on(Controller::EVENT_AFTER_ACTION, [$this, 'afterFilter'], null, false);
} 
```

## Structure
```
 \api                   # here is code for build REST API with OAuth2 server and also public directory, containt index.php
 | \common              # common controllers and models for versions
 | | \controllers       # for tests created only one ProductController
 | | \models            # and one model Product
 | \components          # global for API components (parents for project's controllers, models, filters)
 | |-APIModule.php      # parent for all module/versions
 | |-ActiveController.php # child of yii's rest ActiveController, parent for all ones in project
 | |-Controller.php     # child of yii's rest Controller
 | | \db                # contain parents for all project's ActiveRecord and ActiveQuery
 | | \filters           
 | | | -OAuth2AccessFilter.php # MAIN IMPROVEMENT: analyze action publicity and scopes to attach filter
 | | |                          # to authorize by access token
 | | \traits            # contain ControllersCommonTrait.php with configuration of filters, used in both rest controllers
 | \config
 | \migrations          # contain migrations for create users table, filling demo products and scopes
 | \models              # have User model
 | \versions            # directory for versions, each version is module as recommend in official yii2 guide
 | | \v1                # created for test 1st version with childs of ProductController and Product model

```  

## Tests
### Conditions

- _Domain_: Use something like `http://localhost/api/index.php/` so you don't need to use vhosts
- _Version_: `v1`
- _API point_: `/products`, `/products/<id>`, `/products/custom`, `/products/protected`
- _User_: login: `admin@api.loc`, pass: `123123123`
- _Scopes_: default (default scope, CO :)), custom, protected - for accessing to /products/custom and /products/protected point

### Description
For test have been created [active rest controller](http://www.yiiframework.com/doc-2.0/guide-rest-controllers.html) ProductController for manage Product model.

This controller has the following access rules (in [yii2 format](http://www.yiiframework.com/doc-2.0/guide-security-authorization.html)):
```
	public function accessRules()
	{
		return [
			['allow' => true, 'roles' => ['?']],
			[
			  'allow' => true, 
			  'actions' => ['view','create','update','delete'],
				'roles' => ['@'],
			],
			[
				'allow' => true,
				'actions' => ['custom'],
				'roles' => ['@'],
				'scopes' => ['custom'],
			],
			[
				'allow' => true,
				'actions' => ['protected'],
				'roles' => ['@'],
				'scopes' => ['protected'],
			]
		];
	}
```
Each controller in the api can override method `accessRules` with access rules for itself. Rule can contain additional property - _scope_, this means access token should have addtional permissions.

So, from the rules, we understand that:
* all actions of controller are opened (requests cab be without access token), but
* actions `view`, `create`, `update`, `delete` - available only for authorized users
* and for actions `custom` and `protected` needs additional scopes
 
### Test requests
1\. Request to public api points
```
    curl -i -H "Accept:application/json" -H "Content-Type:application/json" "http://localhost/api/v1/products"
```

2\. Request to get access token
```
curl -i -H "Accept:application/json" -H "Content-Type:application/json" "http://localhost/api/oauth2/token" -XPOST \
-d '{"grant_type":"password","username":"admin@api.loc","password":"123123123","client_id":"testclient","client_secret":"testpass"}'
```

3\. Request to get access token with scopes
```
curl -i -H "Accept:application/json" -H "Content-Type:application/json" "http://localhost/api/oauth2/token" -XPOST \
-d '{"grant_type":"password","username":"admin@api.loc","password":"123123123","client_id":"testclient","client_secret":"testpass","scope":"custom"}'
```

4\. Request to protected api point 
```
curl -i -H "Accept:application/json" -H "Content-Type:application/json" \
"http://localhost/api/v1/products/1?access_token=76f4c0d40347f24a73799335cefb495be9ea364b"
```

## What have been done? ##
1. Got Yii2 framework and created general directory structure (see section [Structure](https://github.com/tsmotta/yii2-oauth2-rest-template#structure)).
2. Configured Yii2 as RESTful Web Service using [official manual](http://www.yiiframework.com/doc-2.0/guide-rest-quick-start.html).
3. Created parent classes to inherit for all components of the project (saved in `components` directory). For more information - look at [Structure](https://github.com/tsmotta/yii2-oauth2-rest-template#structure) section too. Classes [Controller](https://github.com/tsmotta/yii2-oauth2-rest-template/blob/master/api/components/Controller.php) and [ActiveController](https://github.com/ikaras/yii2-oauth2-rest-template/blob/master/api/components/ActiveController.php) are parents for all controllers what use the same trait. [ControllersCommonTrait](https://github.com/ikaras/yii2-oauth2-rest-template/blob/master/api/components/traits/ControllersCommonTrait.php) - it connects (redefine) all needed filters to controller's actions.
4. Directory `common` consists from the common controllers and models for each versions.
5. Then I attached and configured [Filsh's Yii2 OAuth2 extension](https://github.com/Filsh/yii2-oauth2-server), his extension based on widely used [OAuth2 Server Library for PHP](https://bshaffer.github.io/oauth2-server-php-docs/). All detailed information you can find in these repositories. What I've configured:
  - configure module `oauth2` in [common](https://github.com/tsmotta/yii2-oauth2-rest-template/blob/master/api/config/common.php) configurations;
  - adopt method [User::findIdentityByAccessToken](https://github.com/tsmotta/yii2-oauth2-rest-template/blob/master/api/models/User.php#L76) to authorize user by token using `oauth2` module.
6. Developed [OAuth2AccessFilter](https://github.com/tsmotta/yii2-oauth2-rest-template/blob/master/api/components/filters/OAuth2AccessFilter.php) and replaced standard Yii2 AccessFilter (on [ControllersCommonTrait](https://github.com/tsmotta/yii2-oauth2-rest-template/blob/master/api/components/traits/ControllersCommonTrait.php) for all controllers) for more comfortable using Scopes. About it read below.

### Scopes ###
As said in [official OAuth2 Library docs](https://bshaffer.github.io/oauth2-server-php-docs/overview/scope/):
> The use of Scope in an OAuth2 application is often key to proper permissioning. Scope is used to limit the authorization granted to the client by the resource owner. The most popular use of this is Facebook’s ability for users to authorize a variety of different functions to the client (“access basic information”, “post on wall”, etc).

So, each token could have specific permissions to run corresponding API point. In out case, API point is actions.

In Yii2 we already have the tool to control access to particular action by user role, so I decided to expand it functionality to define scopes for action. 

As result was created [OAuth2AccessFilter](https://github.com/tsmotta/yii2-oauth2-rest-template/blob/master/api/components/filters/OAuth2AccessFilter.php) which copy (not inherit, structure not allow to inherit) of standard AccessFilter logic to work with rules with additional logic to process scopes for private action by [means of `oauth2` module](https://github.com/tsmotta/yii2-oauth2-rest-template/blob/master/api/components/filters/OAuth2AccessFilter.php#L59).

So, if user's role allows him to run action (API point), than will check if user's token has needed scopes to do it (if action has defined limited scopes). Example of using you can find in [ProductController](https://github.com/tsmotta/yii2-oauth2-rest-template/blob/master/api/common/controllers/ProductController.php#L36) and it means that only authorized users with token which contain `custom` scope can have access to action `custom`.
