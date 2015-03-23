<?php
    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */

    require_once "src/Task.php";
    require_once "src/Category.php";

    $DB = new PDO('pgsql:host=localhost;dbname=to_do_test');

    class CategoryTest extends PHPUnit_Framework_TestCase
    {
        protected function tearDown()
        {
            Task::deleteAll();
            Category::deleteAll();
        }


        function testGetName()
        {
            //Arrange
            $name = "Clean the kitchen";
            $test_category = new Category($name);

            //Act
            $result = $test_category->getName();

            //Assert
            $this->assertEquals($name, $result);
        }

        function testSetName()
        {
            $name = "Clean the kitchen";
            $test_category = new Category($name);

            //Act
            $test_category->setName("Wash the dog");
            $result = $test_category->getName();

            //Assert
            $this->assertEquals("Wash the dog", $result);
        }

        function testGetId()
        {
            //Arrange
            $id = 1;
            $name = "Kitchen chores";
            $test_category = new Category($name, $id);

            //Act
            $result = $test_category->getId();

            //Assert
            $this->assertEquals(1, $result);
        }

        function testSetId()
        {
            //Arrange
            $id = 1;
            $name = "Kitchen chores";
            $test_category = new Category($name, $id);

            //Act
            $test_category->setId(2);

            //Assert
            $result = $test_category->getId();
            $this->assertEquals(2, $result);
        }

        function testSave()
        {
            //Arrange
            $name = "Work stuff";
            $id = 1;
            $test_category = new Category($name, $id);
            $test_category->save();

            //Act
            $result = Category::getAll();

            //Assert
            $this->assertEquals($test_category, $result[0]);
        }

        function testSaveSetsId()
        {
            //Arrange
            $name = "Work stuff";
            $id = 1;
            $test_category = new Category($name, $id);

            //Act
            $test_category->save();

            //Assert
            $this->assertEquals(true, is_numeric($test_category->getId()));
        }

        function testGetAll()
        {
            //Arrange
            $name = "Work stuff";
            $id = 1;
            $test_category = new Category($name, $id);
            $test_category->save();
            $name2 = "Home stuff";
            $id2 = 2;
            $test_category2 = new Category($name2, $id2);
            $test_category2->save();
            //Act
            $result = Category::getAll();
            //Assert
            $this->assertEquals([$test_category, $test_category2], $result);
        }

        function testDeleteAll()
       {
           //Arrange
           $name = "Wash the dog";
           $id = 1;
           $test_category = new Category($name, $id);
           $test_category->save();
           $name2 = "Water the lawn";
           $id2 = 2;
           $test_category2 = new Category($name2, $id2);
           $test_category2->save();

           //Act
           Category::deleteAll();

           //Assert
           $result = Category::getAll();
           $this->assertEquals([], $result);
       }

       function testFind()
        {
            //Arrange
            $name = "Wash the dog";
            $id = 1;
            $test_category = new Category($name, $id);
            $test_category->save();
            $name2 = "Home stuff";
            $id2 = 2;
            $test_category2 = new Category($name2, $id2);
            $test_category2->save();

            //Act
            $result = Category::find($test_category->getId());

            //Assert
            $this->assertEquals($test_category, $result);
        }

        function testUpdate()
        {
            //Arrange
            $name = "Work stuff";
            $id = 1;
            $test_category = new Category($name, $id);
            $test_category->save();
            $new_name = "Home stuff";
            //Act
            $test_category->update($new_name);
            //Assert
            $this->assertEquals("Home stuff", $test_category->getName());
        }

        function testDeleteCategory()
        {
            //Arrange
            $name = "Work stuff";
            $id = 1;
            $test_category = new Category($name, $id);
            $test_category->save();
            $name2 = "Home stuff";
            $id2 = 2;
            $test_category2 = new Category($name2, $id2);
            $test_category2->save();
            //Act
            $test_category->delete();
            //Assert
            $this->assertEquals([$test_category2], Category::getAll());
        }

        function testAddTask()
        {
            //Arrange
            $name = "Work stuff";
            $id = 1;
            $test_category = new Category($name, $id);
            $test_category->save();

            $description = "File Reports";
            $id2 = 2;
            $test_task = new Task($description, $id2);
            $test_task->save();
            //Act
            $test_category->addTask($test_task);
            //Assert
            $this->assertEquals($test_category->getTasks(), [$test_task]);
        }

        function testGetTasks()
        {
            //Arrange
            $name = "Home stuff";
            $id = 1;
            $test_category = new Category($name, $id);
            $test_category->save();

            //Act
            $description = "Wash the dog";
            $id2 = 2;
            $test_task = new Task($description, $id2);
            $test_task->save();


            $description2 = "Take out the trash";
            $id3 = 3;
            $test_task2 = new Task($description2, $id3);
            $test_task2->save();

            $test_category->addTask($test_task);
            $test_category->addTask($test_task2);

            //Assert
            $this->assertEquals($test_category->getTasks(), [$test_task, $test_task2]);
        }

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
            $test_category->addTask($test_task);
            $test_category->delete();

            //Assert
            $this->assertEquals([], $test_task->getCategories());
        }
    }
?>
