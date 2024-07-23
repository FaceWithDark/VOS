#include "chatbot.hpp"
#include <fmt/core.h>

VOTChatbot::VOTChatbot() 
{
    // Initialization code if needed
}

std::string VOTChatbot::respond(const std::string& query) 
{
    return processQuery(query);
}

std::string VOTChatbot::processQuery(const std::string& query) 
{
    // Simple NLP processing (for demonstration purposes)
    if (query == "Hello") {
        return fmt::format("Hello! How can I assist you today?");
    } 
    else if (query == "How do I get to the [page name]?") {
        return fmt::format("You can get to [page name] by [action to get there].");
    } 
    else {
        return fmt::format("I'm sorry, I don't understand the query: {}", query);
    }
}
