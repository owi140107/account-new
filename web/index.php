<?php
    // web/index.php

    require_once __DIR__.'/../vendor/autoload.php';
    require_once __DIR__.'/delegates/auth_delegate.php';
    require_once __DIR__.'/delegates/user_delegate.php';
    require_once __DIR__.'/delegates/friendship_delegate.php';
    
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Session\Session;

    $app = new Silex\Application();
    $app['debug'] = true;

    // Service provider registrations go here
    $app->register(new Silex\Provider\SessionServiceProvider());
    $app->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => __DIR__.'/views',
    ));

    // Before
    $app->before(function(Request $request) {
        $request->getSession()->start();
    });

    // Routes go here
    $app->get('/', function(Request $request) use ($app) {
        return "<h1>Hello World</h1>";
    });

    $app->get('/login', function(Request $request) use ($app) {
        return $app['twig']->render('login.twig', array());
    });

    $app->post('/login', function(Request $request) use ($app) {
        // Grab the form data
        $formEmail = $request->get('email');
        $formPassword = $request->get('password');
        
        // Validation
        $errors = array();
        $user = get_user_by_email($formEmail);
        
        if ($user == null) {
            
            $errors['email'] = "This email is not registered.";
            $model = array('email' => $formEmail, 'errors' => $errors);
            return $app['twig']->render('login.twig', $model);
            
        } else if (!correct_password_for_user($user, $formPassword)) {
            
            $errors['password'] = "This password is incorrect.";
            $model = array('email' => $formEmail, 'errors' => $errors);
            return $app['twig']->render('login.twig', $model);
            
        } else {
            
            // Login here
            $app['session']->set('id', $user['id']);
            $app['session']->set('name', $user['name']);
            $app['session']->set('email', $user['email']);
            $app['session']->set('avatar', $user['avatar_path']);
            return $app->redirect('/account/web/settings');
            
        }
    });

    $app->post('/logout', function(Request $request) use ($app) {
        $app['session']->invalidate();
        return $app['twig']->render('logout.twig', array());
    });
    
    $app->get('/greeting/{person}', function(Request $request, $person) use ($app) {
        return $app['twig']->render('hello.twig', array('name'=>$person));
    });

    $app->get('/settings', function(Request $request) use ($app) {
        if (!$app['session']->has('id')) {
            return $app->redirect('/account/web/login');
        } else {
            return $app['twig']->render('settings.twig', array());
        }
    });

    $app->get('/friends', function(Request $request) use ($app) {
        if (!$app['session']->has('id')) {
            return $app->redirect('/account/web/login');
        }
        
        $friend_requests = get_friend_request_users($app['session']->get('id'));
        $friends = get_friend_users($app['session']->get('id'));
        $model = array("friend_requests" => $friend_requests, "friends" => $friends);
        return $app['twig']->render('friends.twig', $model);
    });

    $app->post('/respond', function(Request $request) use ($app) {
        if (!$app['session']->has('id')) {
            return $app->redirect('/account/web/login');
        }
        
        $id = $app['session']->get('id');
        $user_id = $request->get('user-id');
        if (null != $request->get('accept')) {
            // The request was accepted
            accept_friend_request($id, $user_id);
        } else if (null != $request->get('ignore')) {
            // The request was ignored
            delete_friend_request($id, $user_id);
        }
        
        return $app->redirect('/account/web/friends');
    });

    $app->post('/search', function(Request $request) use ($app) {
        if (!$app['session']->has('id')) {
            return $app->redirect('/account/web/login');
        }
        
        $search_input = $request->get('searchTerm');
        $results = search_for_users($search_input);
        $model = array("results" => $results);
        
        return $app['twig']->render('search_results.twig', $model);
    });

    $app->get('/view/{user_id}', function(Request $request, $user_id) use ($app) {
        $user = get_user($user_id);
        $model = array("user" => $user);
        
        return $app['twig']->render('profile.twig', $model);
    });

    $app->post('/view', function(Request $request) use ($app) {
        $user_id = $request->get('user-id');
        
        return $app->redirect('/account/web/view/'.$user_id);
    });

    $app->post('/info/basic', function(Request $request) use ($app) {
        if (!$app['session']->has('id')) {
            return $app->redirect('/account/web/login');
        }
        
        $id = $app['session']->get('id');
        $name = $request->get('name');
        update_user_name($id, $name);
        
        return $app->redirect('/account/web/settings');
    }); 

    $app->post('/info/avatar', function(Request $request) use ($app) {
        if (!$app['session']->has('id')) {
            return $app->redirect('/account/web/login');
        }
        
        $avatarFile = $request->files->get('avatar-file');
        $avatarFile->move(__DIR__.'/images', $avatarFile->getClientOriginalName());
        return $app->redirect('/account/web/settings');
    }); 

    // Run the app
    $app->run();
?>