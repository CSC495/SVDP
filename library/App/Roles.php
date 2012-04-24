<?php

// Provides constants for defining the role of the user
class App_Roles
{
    // Change the string values to match with database values
    const GENERAL   = "G";      // Parent Role (Shared among all others)
    const MEMBER    = "M";      // Standard member
    const TREASURER = "T";      // Treasurer
    const ADMIN     = "A";      // Admin
}