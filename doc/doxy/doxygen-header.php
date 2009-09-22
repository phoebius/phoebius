<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright notice
 *
 ************************************************************************************************/

/**
 * @mainpage Phoebius framework
 *
 * @defgroup App Groups that are responsible for high level of application lifecycle
 *
 * @defgroup Routing Package for preparing, routing and dispatching the request to the target request handler
 * @ingroup App
 *
 * @defgroup WebRouting Routers for handling typically web requests
 * @ingroup Routing
 *
 * @defgroup OrmRouting Routers for handling requests that contextually depend on Orm
 * @ingroup Routing
 *
 * @defgroup RoutingExceptions
 * @ingroup Routing
 *
 * @defgroup RewriteRoutingExceptions
 * @ingroup RoutingExceptions
 *
 * @defgroup RouteHandleExceptions
 * @ingroup RoutingExceptions
 *
 * @defgroup Server Wrappers over server information
 * @ingroup App
 *
 * @defgroup Web Wrappers over request coming from a web
 * @ingroup App
 *
 *
 * @defgroup Core Classes that represent the framework's infrastructure and agreements
 *
 * @defgroup Bootstrap Internal handlers for autoloading classes
 * @ingroup Core
 *
 * @defgroup CoreExceptions
 * @ingroup Core
 *
 * @defgroup Patterns Various base classes
 * @ingroup Core
 *
 * @defgroup CoreTypes Implementation of base types' wrappers
 * @ingroup Core
 *
 * @defgroup BuiltInCoreTypes Wrappers over primitive types built in php core
 * @ingroup CoreTypes
 *
 * @defgroup ComplexCoreTypes Wrappers over complex types
 * @ingroup CoreTypes
 *
 *
 * @defgroup Dal Database abstraction layer
 *
 * @defgroup Condition OO wrappers over SQL expressions
 * @ingroup Dal
 *
 * @defgroup ExpressionPredicates OO predicates for SQL conditions
 * @ingroup Condition
 *
 * @defgroup DB Unified RDBMS interfaces
 * @ingroup Dal
 *
 * @defgroup Transaction Unified RDBMS transaction interface
 * @ingroup DB
 *
 * @defgroup DalExceptions
 * @ingroup Dal
 *
 * @defgroup Query OO wrappers over SQL queries
 * @ingroup Dal
 *
 * @defgroup SelectQueryHelpers
 * @ingroup Query
 *
 * @defgroup Sql OO wrappers over various SQL particles
 * @ingroup Dal
 *
 *
 * @defgroup Mvc Model-view-controller implementation
 *
 * @defgroup ActionResults Wrappers over various results that actions produce while execution
 * @ingroup Mvc
 *
 * @defgroup MvcExceptions
 * @ingroup Mvc
 *
 * @defgroup PhpLayout Simple template engine based on raw PHP and classes
 * @ingroup Mvc
 *
 *
 * @defgroup Orm Object relational mapping engine - the heart of Phoebius framework
 *
 * @defgroup Dao Data access objects - a unified interface for accessing ORM objectes wherever they are stored
 * @ingroup Orm
 *
 * @defgroup RelationshipDao Interface for accessing relative ORM objects and sets of objects
 * @ingroup Dao
 *
 * @defgroup RelationshipDaoWorkers Internal workers
 * @ingroup RelationshipDao
 *
 * @defgroup OrmDomain Implementation of ORM objects aggregator
 * @ingroup Orm
 *
 * @defgroup OrmDomainImporter Various handlers to create an internal representation of ORM objects graph from simple definitions (e.g., XML)
 * @ingroup OrmDomain
 *
 * @defgroup XmlOrmDomainImporter Handler for creating an internal representation of ORM objects graph from an XML
 * @ingroup OrmDomainImporter
 *
 * @defgroup OrmCodeGenerator Package for generating a code from an ORM object graph
 * @ingroup OrmDomain
 *
 * @defgroup OrmExceptions
 * @ingroup Orm
 *
 * @defgroup OrmMap Mapper of raw RDBMS (or any other source, accessed by DAO provider) data and ORM objects' types
 * @ingroup Orm
 *
 * @defgroup OrmModel OO wrappers over internal representation of ORM objects' graph (inner structures and dependencies)
 * @ingroup Orm
 *
 * @defgroup OrmModelExceptions
 * @ingroup OrmOrmModel
 *
 * @defgroup OrmTypes OO wrappers over various ORM entity property types
 * @ingroup Orm
 *
 * @defgroup BaseOrmTypes Representation of basic ORM types
 * @ingroup OrmTypes
 *
 * @defgroup CustomOrmTypes Representatation of various complex types
 * @ingroup OrmTypes
 *
 * @defgroup PrimitiveOrmTypes Representation of primitive (built-in) types
 * @ingroup OrmTypes
 *
 *
 * @defgroup Test Base classes for framework unit tests
 *
 *
 * @defgroup Utils Various helper packages
 *
 * @defgroup Cipher
 * @ingroup Utils
 *
 * @defgroup Stream
 * @ingroup Utils
 *
 * @defgroup Image
 * @ingroup Utils
 *
 * @defgroup Net
 * @ingroup Utils
 *
 * @defgroup Sys
 * @ingroup Utils
 *
 * @defgroup SysConsole
 * @ingroup Sys
 *
 * @defgroup SysLoggers
 * @ingroup Sys
 *
 *
**/

?>