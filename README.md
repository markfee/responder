Responder Package.
========================

Use this package to add standard responses to  APIs, so that all APIs are consistent in the 
way they return data

  Add the following repositores to your root composer.json

  ``` json
  "repositories": {
    "responder":      { "type": "vcs",  "url": "git@fred:itsd_packages/responder.git"     }
  }
  ```
and add the dependency on markfee/responder
  
  ``` json
  "require": {
    "markfee/responder": "~6.0",
  }
  ```

 add the provider to config/app.php:

  ``` php
        Markfee\Responder\ResponderServiceProvider::class,
  ```


== To convert from pre version 6 of the responder

  ``` php
  	// Remove all paths like this from the namespace includes.
  	-use Markfee\Responder\Repository\RepositoryResponse;
	-use Markfee\Responder\Transformer\TransformerInterface;

	
	//Remove extends RepositoryResponse
	-class PeopleCollection extends RepositoryResponse {
	+class PeopleCollection  {

	// Add in the following trait (this replaces the inherited members of the deprecated RepositoryResponse)
+    use \Responder\ResponderTrait;


	// Remove TransformerInterface $transformer from the constructor of your class
	// (you can still pass in a transformer, but you will have to call setTransformer in your constructor)

-    function __construct(TransformerInterface $transformer) {
-        parent::__construct($transformer);
+    function __construct($transformer) {
-        parent::__construct($transformer);
+        $this->setTransformer($transformer);
