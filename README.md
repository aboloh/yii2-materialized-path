# Yii2 Materialized Path Behavior

Implementation of materialized path algorithm for storing the trees in DB tables.
  
[![Packagist Version](https://img.shields.io/packagist/v/paulzi/yii2-materialized-path.svg)](https://packagist.org/packages/paulzi/yii2-materialized-path)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/paulzi/yii2-materialized-path/master.svg)](https://scrutinizer-ci.com/g/paulzi/yii2-materialized-path/?branch=master)
[![Build Status](https://img.shields.io/travis/paulzi/yii2-materialized-path/master.svg)](https://travis-ci.org/paulzi/yii2-materialized-path)
[![Total Downloads](https://img.shields.io/packagist/dt/paulzi/yii2-materialized-path.svg)](https://packagist.org/packages/paulzi/yii2-materialized-path)

## Install

Install via Composer:

```bash
composer require paulzi/yii2-materialized-path
```

or add

```bash
"paulzi/yii2-materialized-path" : "^1.0"
```

to the `require` section of your `composer.json` file.

## Migrations

Sample migrations are in the folder `sample-migrations`:

- `m150722_150000_single_tree.php` - for single tree tables;
- `m150722_150100_multiple_tree.php` - for multiple tree tables.

## Configuring

```php
use paulzi\materializedpath\MaterializedPathBehavior;

class Sample extends \yii\db\ActiveRecord
{
    public function behaviors() {
        return [
            [
                'class' => MaterializedPathBehavior::className(),
                // 'treeAttribute' => 'tree',
            ],
        ];
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }
}
```

Optional you can setup Query for finding roots:

```php
class Sample extends \yii\db\ActiveRecord
{
    public static function find()
    {
        return new SampleQuery(get_called_class());
    }
}
```

Query class:

```php
use paulzi\materializedpath\MaterializedPathQueryTrait;

class SampleQuery extends \yii\db\ActiveQuery
{
    use MaterializedPathQueryTrait;
}
```

## Options

- `$pathAttribute = 'path'` - setup path attribute in table schema.
- `$depthAttribute = 'depth'` - setup depth attribute in table schema.
- `$sortAttribute = 'sort'` - setup sort attribute in table schema.
- `$itemAttribute = null` - setup item attribute in table schema for get path path, if the value is not set - using the primary key.
- `$treeAttribute = null` - setup tree attribute for multiple tree, when item attribute is not primary key.
- `$delimiter = '/'` - delimiter of path items.
- `$step = 100` - gap size between elements.
- `$rootDepthValue = 0` - setup value of `$depthAttribute` for root nodes.

## Usage

### Selection

**Getting the root nodes**

If you connect `NestedSetsQueryTrait`, you can get all the root nodes:

```php
$roots = Sample::find()->roots()->all();
```

**Getting ancestors of a node**

To get ancestors of a node:

```php
$node11 = Sample::findOne(['name' => 'node 1.1']);
$parents = $node11->parents; // via relation
$parents = $node11->getParents()->all(); // via query
$parents = $node11->getParents(2)->all(); // get 2 levels of ancestors
```

To get parent of a node:

```php
$node11 = Sample::findOne(['name' => 'node 1.1']);
$parent = $node11->parent; // via relation
$parent = $node11->getParent()->one(); // via query
```

To get root of a node:

```php
$node11 = Sample::findOne(['name' => 'node 1.1']);
$root = $node11->root; // via relation
$root = $node11->getRoot()->one(); // via query
```

**Getting descendants of a node**

To get all the descendants of a node:

```php
$node11 = Sample::findOne(['name' => 'node 1.1']);
$descendants = $node11->descendants; // via relation
$descendants = $node11->getDescendants()->all(); // via query
$descendants = $node11->getDescendants(2, true)->all(); // get 2 levels of descendants and self node
```

To get all the descendants of a node at one query:

```php
$node11 = Sample::findOne(['name' => 'node 1.1']);
$tree = $node11->getTree(); // self node will be at the root of tree
$tree = $node11->getTree(2); // get 2 levels of descendant and self node at the root of tree
```

To get the children of a node:

```php
$node11 = Sample::findOne(['name' => 'node 1.1']);
$children = $node11->children; // via relation
$children = $node11->getChildren()->all(); // via query
```

**Getting the leaves nodes**

To get all the leaves of a node:

```php
$node11 = Sample::findOne(['name' => 'node 1.1']);
$leaves = $node11->leaves; // via relation
$leaves = $node11->getLeaves(2)->all(); // get 2 levels of leaves via query
```

**Getting the neighbors nodes**

To get the next node:

```php
$node11 = Sample::findOne(['name' => 'node 1.1']);
$next = $node11->next; // via relation
$next = $node11->getNext()->one(); // via query
```

To get the previous node:

```php
$node11 = Sample::findOne(['name' => 'node 1.1']);
$prev = $node11->prev; // via relation
$prev = $node11->getPrev()->one(); // via query
```

### Some checks

```php
$node1 = Sample::findOne(['name' => 'node 1']);
$node11 = Sample::findOne(['name' => 'node 1.1']);
$node11->isRoot() - return true, if node is root
$node11->isLeaf() - return true, if node is leaf
$node11->isChildOf($node1) - return true, if node11 is child of $node1
```


### Modifications

To make a root node:

```php
$node11 = new Sample();
$node11->name = 'node 1.1';
$node11->makeRoot()->save();
```

*Note: if you allow multiple trees and attribute `tree` is not set, it automatically takes the primary key value.*

To prepend a node as the first child of another node:

```php
$node1 = Sample::findOne(['name' => 'node 1']);
$node11 = new Sample();
$node11->name = 'node 1.1';
$node11->prependTo($node1)->save(); // inserting new node
```

To append a node as the last child of another node:

```php
$node11 = Sample::findOne(['name' => 'node 1.1']);
$node12 = Sample::findOne(['name' => 'node 1.2']);
$node12->appendTo($node11)->save(); // move existing node
```

To insert a node before another node:

```php
$node13 = Sample::findOne(['name' => 'node 1.3']);
$node12 = new Sample();
$node12->name = 'node 1.2';
$node12->insertBefore($node13)->save(); // inserting new node
```

To insert a node after another node:

```php
$node13 = Sample::findOne(['name' => 'node 1.3']);
$node14 = Sample::findOne(['name' => 'node 1.4']);
$node14->insertAfter($node13)->save(); // move existing node
```

To delete a node with descendants:

```php
$node11 = Sample::findOne(['name' => 'node 1.1']);
$node11->delete(); // delete node, children come up to the parent
$node11->deleteWithChildren(); // delete node and all descendants 
```

To normalize values of `$sortAttribute` for node's children:

```php
$node11 = Sample::findOne(['name' => 'node 1.1']);
$node11->reorderChildren(); // children's $sortAttribute will be started from `0`
```
