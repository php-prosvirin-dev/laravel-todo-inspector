## Database

The package uses its own table `todo_inspector_tasks` by default.

You can change the table name in the config:

```php
// config/todo-inspector.php
'table_name' => 'your_custom_table_name',