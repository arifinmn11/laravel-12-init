<?php

return [

    /*
    |--------------------------------------------------------------------------
    | MCP Server Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your MCP server. This name is used when
    | registering the server with MCP clients.
    |
    */

    'name' => env('MCP_SERVER_NAME', 'Laravel MCP Server'),

    /*
    |--------------------------------------------------------------------------
    | MCP Server Description
    |--------------------------------------------------------------------------
    |
    | A brief description of what your MCP server provides.
    |
    */

    'description' => env('MCP_SERVER_DESCRIPTION', 'Laravel application exposing tools and resources via Model Context Protocol'),

    /*
    |--------------------------------------------------------------------------
    | MCP Server Version
    |--------------------------------------------------------------------------
    |
    | The version of your MCP server implementation.
    |
    */

    'version' => env('MCP_SERVER_VERSION', '1.0.0'),

    /*
    |--------------------------------------------------------------------------
    | Tools
    |--------------------------------------------------------------------------
    |
    | Register your MCP tools here. Tools are callable functions that AI
    | agents can invoke.
    |
    */

    'tools' => [
        \App\Boost\Tools\SystemStats::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Resources
    |--------------------------------------------------------------------------
    |
    | Register your MCP resources here. Resources are data sources that AI
    | agents can read.
    |
    */

    'resources' => [
        // Add your resources here
    ],

    /*
    |--------------------------------------------------------------------------
    | Prompts
    |--------------------------------------------------------------------------
    |
    | Register your MCP prompts here. Prompts are pre-defined templates
    | that can guide AI interactions.
    |
    */

    'prompts' => [
        // Add your prompts here
    ],

];
