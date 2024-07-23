#include "chatbot.hpp"
#include <fmt/core.h>

VOTChatbot::VOTChatbot() 
{
    // Initialisation process
    fmt::print("Initialising VOT chatbot...\n");

    // Load pre-defined responses
    predefinedResponses["Hello"] = "Hello! How can I assist you today?";
    predefinedResponses["How do I get to the [page name]?"] = "You can get to [page name] by [action to get there]";

    // Initialises queries with both case-sensitive and case-insensitve strings
    greetingQuery = {"hi", "Hi", "hello", "Hello", "hey", "Hey"};
    directionQuery = {"how do I get to the [page name]?", "How do I get to the [page name]?", "where is [page name]?", "Where is [page name]?"};

    // Finish initialisation process
    fmt::print("VOT Chatbot initialised successfully\n");
}

std::string VOTChatbot::respondQuery(const std::string& query) 
{
    return processQuery(query);
}

std::string VOTChatbot::processQuery(const std::string& query) 
{
    // Check for greeting queries
    if(isQueryMatch(greetingQuery, query)) 
    {
        return predefinedResponses["Hello"];
    }

    // Check for direction queries
    if(isQueryMatch(directionQuery, query))
    {
        return predefinedResponses["How do I get to the [page name]?"];
    }
    
    // Output error message if user ask questions that is/are not belongs to the queries
    return fmt::format("I'm sorry, I don't understand the query: {}", query);
}

bool VOTChatbot::isQueryMatch(const std::vector<std::string>& queries, const std::string& query)
{
    // Iterate through each string in the queries vector
    for(const auto& queryString : queries)
    {
        // Check if the current defined string matches the input query from the user
        if(queryString == query)
        {
            // Found the matching query to get output
            return true;
        }
    }
    // No matching query found to get output
    return false;
}
