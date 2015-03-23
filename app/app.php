<?php
    /**
    * @backupGlobals disabled
    *@backupStaticAttribute disabled
    */
    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../src/Task.php";
    require_once __DIR__."/../src/Category.php";

    $app = new Silex\Application();
    $app['debug'] = TRUE;

    $DB = new PDO('pgsql:host=localhost;dbname=to_do');

    $app->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => __DIR__.'/../views'
    ));

    use Symfony\Component\HttpFoundation\Request;
    Request::enableHttpMethodParameterOverride();

    $app->get("/", function() use ($app){
        return $app['twig']->render('index.html.twig');
    });

    $app->get("/tasks", function() use ($app) {
        return $app['twig']->render('tasks.html.twig', array('tasks' => Task::getAll()));
    });


    $app->get("/tasks/{id}", function($id) use ($app) {
        $task = Task::find($id);
        return $app['twig']->render('task.html.twig', array('task' => $task, 'categories'=>$task->getCategories(), 'all_categories'=>Category::getAll()));
    });

    $app->post("/tasks", function() use ($app) {
        $description = $_POST['description'];
        $task = new Task($description);
        $task->save();
        return $app['twig']->render('tasks.html.twig', array('tasks' => Task::getAll()));
    });


//ASK DIANE: Why do we need 'tasks' -> Task::getAll() in this route??
    $app->post("/add_categories", function() use ($app) {
        $task_id = $_POST['task_id'];
        $task = Task::find($task_id);
        $category_id = $_POST['category_id'];
        $category = Category::find($category_id);
        $task->addCategory($category);
        return $app['twig']->render('task.html.twig', array('task' => $task, 'tasks' => Task::getAll(), 'categories' => $task->getCategories(), 'all_categories' => Category::getAll()));
    });

    $app->get("/tasks/{id}/edit", function($id) use ($app) {
        $task = Task::find($id);
        return $app['twig']->render('task_edit.html.twig', array('task' => $task));
    });

    $app->patch("/tasks/{id}", function($id) use ($app) {
        $description = $_POST['description'];
        $task = Task::find($id);
        $task->update($description);
        return $app['twig']->render('task.html.twig', array('task' => $task,'categories' => $task->getCategories(), 'all_categories' => Category::getAll()));
    });

    $app->delete("/tasks/{id}", function($id) use ($app) {
        $task = Task::find($id);
        $task->delete();
        return $app['twig']->render('tasks.html.twig', array('tasks'=>Task::getAll()));
    });


//DELETE all tasks, then route back to root

    $app->delete("/delete_tasks", function() use ($app) {
        Task::deleteAll();
        return $app['twig']->render('index.html.twig');
    });


//CATEGORIES ************

    $app->get("/categories", function() use ($app) {
        return $app['twig']->render('categories.html.twig', array('categories' => Category::getAll()));
    });

    $app->post("/categories", function() use ($app) {
        $name = $_POST['name'];
        $category = new Category($name);
        $category->save();
        return $app['twig']->render('categories.html.twig', array('categories' => Category::getAll()));
    });

    $app->get("/categories/{id}", function($id) use ($app) {
        $category = Category::find($id);
        return $app['twig']->render('category.html.twig', array('category' => $category, 'tasks' => $category->getTasks(), 'all_tasks' => Task::getAll()));
    });

    $app->get("/categories/{id}/edit", function($id) use ($app) {
        $category = Category::find($id);
        return $app['twig']->render('category_edit.html.twig', array('category' => $category));
    });

    $app->patch("/categories/{id}", function($id) use ($app) {
        $name = $_POST['name'];
        $category = Category::find($id);
        $category->update($name);
        return $app['twig']->render('category.html.twig', array ('category'=>$category, 'tasks'=>$category->getTasks(), 'all_tasks' => Task::getAll()));
    });

//ASK DIANE: Why do we need 'categories' => Category::getAll() here?
    $app->post("/add_tasks", function() use ($app) {
        $category = Category::find($_POST['category_id']);
        $task = Task::find($_POST['task_id']);
        $category->addTask($task);
        return $app['twig']->render('category.html.twig', array('category' => $category, 'categories' => Category::getAll(), 'tasks' => $category->getTasks(), 'all_tasks' => Task::getAll()));
    });

    $app->delete("/delete_categories", function() use ($app) {
        Category::deleteAll();
        return $app['twig']->render('index.html.twig');
    });

    $app->delete("/categories/{id}", function($id) use ($app) {
        $category = Category::find($id);
        $category->delete();
        return $app['twig']->render('categories.html.twig', array('categories'=>Category::getAll()));
    });

    $app->post("/tasks/{id}", function($id) use ($app) {
        $task = Task::find($id);
        $description = $task->getDescription();
        if(isset($_POST['status']))
        {
            $status = true;
        }
        else
        {
            $status = false;
        }
        $task = new Task($description,$status);
        $task->save();
        return $app['twig']->render('tasks.html.twig', array('tasks' => Task::getAll()));
    });



    return $app;


?>
