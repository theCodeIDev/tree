## Project "Tree Example"

The "Tree Example" project is an application that visualizes arbitrary "trees" of text nodes and provides users with the ability to add new nodes (roots) and remove existing ones.

### Usage

The "Tree Example" application offers the following features:

- Displaying the tree structure of text nodes.
- Adding new nodes as roots to the tree.
- Removing existing nodes from the tree.

### Example Link

You can find an example of the "Tree Example" application [here](https://wizxpert.net/solid/).

### Database Table Creation

To store the tree structure and node information, you can create a MySQL database table called `nodes`. The following script creates the table with the necessary columns:

```sql
DROP TABLE IF EXISTS `nodes`;
CREATE TABLE `nodes` (
  `id`          int(11)     NOT NULL,
  `parent_id`   int(11)     NOT NULL DEFAULT '0',
  `hidden`      int(11)     NOT NULL DEFAULT '0',
  `name`        varchar(64) NOT NULL DEFAULT '',
  `user_uid`    varchar(32) NOT NULL DEFAULT ''
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251;

```

| Column    | Description                                                     |
|-----------|-----------------------------------------------------------------|
| id        | An integer column representing the node ID.                     |
| parent_id | An integer column representing the ID of the parent node.        |
| hidden    | An integer column indicating whether the node is hidden.         |
| name      | A varchar column representing the name or label of the node.     |
| user_uid  | A varchar column representing the unique identifier of the user. |
