## Authentication

The package uses HTTP Basic Auth. Configure credentials in your `.env`:

```env
TODO_INSPECTOR_LOGIN=admin
TODO_INSPECTOR_PASSWORD=your-secure-password
```

## Database

The package uses its own table `todo_inspector_tasks` by default.

You can change the table name in the config:

```php
// config/todo-inspector.php
'table_name' => 'your_custom_table_name',