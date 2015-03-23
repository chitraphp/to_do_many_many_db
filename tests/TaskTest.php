<?php
    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */
    require_once "src/Task.php";
    require_once "src/Category.php";
    $DB = new PDO('pgsql:host=localhost;dbname=to_do_test');
    class TaskTest extends PHPUnit_Framework_TestCase
    {
        protected function tearDown()
        {
            Task::deleteAll();
            Category::deleteAll();
        }
        //Initialize a Task with a description and be able to get it back out of the object using getDescription().
        function testGetDescription()
        {
            //Arrange
            $description = "Do dishes.";
            $test_task = new Task($description);
            //No need to save here because we are communicating with the object only and not the database.
            //Act
            $result = $test_task->getDescription();
            //Assert
            $this->assertEquals($description, $result);
        }
        function testSetDescription()
        { //can I change the description in the object with setDescription() after initializing it?
            //Arrange
            $description = "Do dishes.";
            $test_task = new Task($description);
            //No need to save here because we are communicating with the object only and not the database.
            //Act
            $test_task->setDescription("Drink coffee.");
            $result = $test_task->getDescription();
            //Assert
            $this->assertEquals("Drink coffee.", $result);
        }
        //Next, let's add the Id. property to our Task class. Like any other property it needs a getter and setter.
        //Create a Task with the id in the constructor and be able to get the id back out.
        function testGetId()
        {
            //Arrange
            $id = 1;
            $description = "Wash the dog";
            $test_task = new Task($description, $id);
            //Act
            $result = $test_task->getId();
            //Assert
            $this->assertEquals(1, $result);
        }
        //Create a Task with the id in the constructor and be able to change its value, and then get the new id out.
        function testSetId()
        {
            //Arrange
            $id = 1;
            $description = "Wash the dog";
            $test_task = new Task($description, $id);
            //Act
            $test_task->setId(2);
            //Assert
            $result = $test_task->getId();
            $this->assertEquals(2, $result);
        }
        //CREATE - save method stores all object data in tasks table.
        function testSave()
        {
            //Arrange
            //create a new task
            $description = "Wash the dog";
            $id = 1;
            $test_task = new Task($description, $id);
            //Act
            //save the task to the database
            //Id should be assigned in database, then stored in object.
            $test_task->save();
            //Assert
            //get all existing tasks back out of the database.
            //The first and only one should hold the same properties as the test task.
            $result = Task::getAll();
            $this->assertEquals($test_task, $result[0]);
        }
        //This test makes sure that after saving not only are the id's equal, they are not null.
        function testSaveSetsId()
        {
            //Arrange
            //create new task
            $description = "Wash the dog";
            $id = 1;
            $test_task = new Task($description, $id);
            //Act
            //save it. Id should be assigned in database, then stored in object.
            $test_task->save();
            //Assert
            //That id in the object should be numeric (not null)
            $this->assertEquals(true, is_numeric($test_task->getId()));
        }
        //READ - All tasks
        //Can't run the previous two tests without getAll().
        //This method should return an array of all Task objects from the tasks table.
        //Since it isn't specifically for only one Task, it is for all, it should be a static method.
        function testGetAll()
        {
            //Arrange
            //Create and save more than one Task object.
            $description = "Wash the dog";
            $id = 1;
            $test_task = new Task($description, $id);
            $test_task->save();
            $description2 = "Water the lawn";
            $id2 = 2;
            $test_task2 = new Task($description2, $id2);
            $test_task2->save();
            //Act
            //Query the database to get all existing saved tasks as objects.
            $result = Task::getAll();
            //Assert
            //We should get our two test tasks back out in $result.
            //Remember the [$thing1, $thing2] notation is used for an array.
            $this->assertEquals([$test_task, $test_task2], $result);
        }
        //Now that we are saving, we need a method to delete everything out of our database too.
        //For our tests to run we need to clear our test database with a deleteAll function after each test.
        //Since this also deals with more than one Task it should be a static method.
        function testDeleteAll()
        {
            //Arrange
            //We need some tasks saved into the database so that we can make sure our deleteAll method removes them all.
            $description = "Wash the dog";
            $id = 1;
            $test_task = new Task($description, $id);
            $test_task->save();
            $description2 = "Water the lawn";
            $id2 = 2;
            $test_task2 = new Task($description2, $id2);
            $test_task2->save();
            //Act
            //Set all tasks on fire. Delete them.
            Task::deleteAll();
            //Assert
            //Now when we call getAll, we should get an empty array because we deleted all tasks.
            $result = Task::getAll();
            $this->assertEquals([], $result);
        }
        //We have Create, Read (all), Delete (all). What's left in CRUD?
        //Read sigular (view a single task),
        //Update (edit an existing singular task),
        //Delete (singular - remove task the method is called on.)
        //All of these require us to be able to select a Task by its unique id. So the FIND method is next.
        //find() method should take an id as input and return the corresponding task.
        //since it must search through all tasks it should be a static method.
        function testFind()
        {
            //Arrange
            //To test a search function we must have some tasks to search through.
            //Create and save 2 tasks.
            $description = "Wash the dog";
            $id = 1;
            $test_task = new Task($description, $id);
            $test_task->save();
            $description2 = "Water the lawn";
            $id2 = 2;
            $test_task2 = new Task($description2, $id2);
            $test_task2->save();
            //Act
            //call the method we intend to write.
            //look through all tasks for the task matching the first task's assigned id number.
            //store the output in $result.
            $result = Task::find($test_task->getId());
            //Assert
            //we should get the same object back out of the search as the one we were looking for if our search works correctly.
            $this->assertEquals($test_task, $result);
        }
        function testUpdate()
        {
            //Arrange
            $description = "Wash the dog";
            $id = 1;
            $test_task = new Task($description, $id);
            $test_task->save();
            $new_description = "Clean the dog";
            //Act
            $test_task->update($new_description);
            //Assert
            $this->assertEquals("Clean the dog", $test_task->getDescription());
        }
        function testDeleteTask()
        {
            //Arrange
            $description = "Wash the dog";
            $id = 1;
            $test_task = new Task($description, $id);
            $test_task->save();
            $description2 = "Water the lawn";
            $id2 = 2;
            $test_task2 = new Task($description2, $id2);
            $test_task2->save();
            //Act
            $test_task->delete();
            //Assert
            $this->assertEquals([$test_task2], Task::getAll());
        }
        //Now add methods to add a category to a task, and get all the categories associated with the current task.
        function testAddCategory()
        {
            //Arrange
            $name = "Work stuff";
            $id = 1;
            $test_category = new Category($name, $id);
            $test_category->save();
            $description = "File reports";
            $id2 = 2;
            $test_task = new Task($description, $id2);
            $test_task->save();
            //Act
            $test_task->addCategory($test_category);
            //Assert
            $this->assertEquals($test_task->getCategories(), [$test_category]);
        }
        function testGetCategories()
        {
            //Arrange
            $name = "Work stuff";
            $id = 1;
            $test_category = new Category($name, $id);
            $test_category->save();
            $name2 = "Volunteer stuff";
            $id2 = 2;
            $test_category2 = new Category($name2, $id2);
            $test_category2->save();
            $description = "File reports";
            $id3 = 3;
            $test_task = new Task($description, $id3);
            $test_task->save();
            //Act
            $test_task->addCategory($test_category);
            $test_task->addCategory($test_category2);
            //Assert
            $this->assertEquals($test_task->getCategories(), [$test_category, $test_category2]);
        }
        //When we call delete on a task it should delete all mention of that task from both the tasks table and the join table.
        function testDelete()
        {
            //Arrange
            $name = "Work stuff";
            $id = 1;
            $test_category = new Category($name, $id);
            $test_category->save();
            $description = "File reports";
            $id2 = 2;
            $test_task = new Task($description, $id2);
            $test_task->save();
            //Act
            $test_task->addCategory($test_category);
            $test_task->delete();
            //Assert
            $this->assertEquals([], $test_category->getTasks());
        }

    }
?>
