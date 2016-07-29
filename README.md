APIFirst Bundle
===============

Provides classes for API-first designed projects with Symfony.

For personnal purposes for the moment.

The goal of this bundle is to help in designing Symfony applications that can be consumed with an API and with an UI as well.

Concept
-------

A Symfony entity is a Resource that has an id. It should implement `ResourceInterface` which just requires implementing a `getId()` method.

Several classes interacts with this resource:

* ORM / ODM classes (EntityManager, Repository)
* Form classes
* Action classes (GET, POST, PUT, PATCH, DELETE, related resources, etc)

A `AbstractResourceHandler` is a service that gives access to the corresponding classes of a specific Resource.

**Here's the flow:**

* The `AbstractResourceHandler` provides form handling. It is HTTP agnostic: you can submit a form from a `Request` or from raw data (array). You can use it in cron jobs, bulk actions, its role is not to send a `Response` but a `Resource`. When the form fails it throws a `ValidationFormException`.
* The Action classes calls the `AbstractResourceHandler` to transform a `Resource` with a `Request`.
* The Action classes can generate a pre-response, in which they can define:
	* What to do on success (redirect to an URL, add flashes for instance, in case of an UI request)
	* Which HTTP status code to reply (in case of an API submission)
* An event-listener will transform this `PreResponse` to the correct response with content-negociation (redirect + flash if the request came from an UI, status code in case of an API request)
* When a `ValidationFormException` is thrown from the `AbstractResourceHandler`, the Action class should:
	* Return a HTTP 200 response code with the form and the errors in case of an UI request
	* Return a HTTP 400 response code with the serialized form errors in case of an API request
	* The `BenTools\ApiFirstBundle\Model\AbstractCRUDAction::submitForm()` method will return the resolved callable `$success` on success; the Form object otherwise.

CSRF Protection
---------------

* FOSRESTBundle can disable CSRF protection on a specific role.
* This is not the best solution since the same User can use both the API and the UI. This means if they have the ROLE_API and logs in on the UI, they won't be CSRF-protected.

**APIFirstBundle** provides another solution:

* When you create your Form Types, don't extend `Symfony\Component\Form\AbstractType` but `BenTools\ApiFirstBundle\Form\ApiFirstAbstractType` instead
* Setup your form with automatic CSRF protection enabling:

```php
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class'      => MyResource::class,
            'csrf_protection' => $this->shouldEnableCSRFProtection(),
        ]);
    }

```

* Declare your form type as a service since its constructor has a dependency on `api_first.api_consumer_detector` 


Form Handling
-------------

When you extend the `BenTools\ApiFirstBundle\Model\AbstractResourceHandler` class, you can call the `getCreationForm`, `getEditionForm` and the `getDeletionForm` methods.

If you're using an UI, it will create a *named* form. On the contrary, if you're posting data on the API, the keys won't be prefixed in the form.

For instance, if you're creating a new Contact resource with the UI, the app will expect the following form params:
```
[
    'contact' => [
        'firstname' => 'John',
        'lastname'  => 'Doe',
    ],
    '_token' => 'zef6rq1g6er8g1re6g81e6fertjh4yu6j4'
];
```

If you're using the API, the app will expect this:
```
[
    'firstname' => 'John',
    'lastname'  => 'Doe',
];
```

Of course we could use non-named forms only. 
But this leads to issues with Symfony's *_token* and *_method* hidden fields that are misunderstood as forms *extra fields*.