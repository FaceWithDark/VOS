#include "chatbot.hpp"
#include <algorithm>
#include <fmt/core.h>

// Constructor to initialise the chatbot
VOTChatbot::VOTChatbot()
{
    greetingKeywords = {
        {"hi", "Hi"},
        {"hello", "Hello"},
        {"hey", "Hey"}
    };
}

// Convert any uppercase string to lowercase string to support both typing format
std::string VOTChatbot::upperToLowerCase(const std::string& keywordString)
{
    std::string lowerCaseKeywordString = keywordString;
    std::transform(lowerCaseKeywordString.begin(), lowerCaseKeywordString.end(), lowerCaseKeywordString.begin(), ::tolower);
    return lowerCaseKeywordString;
}

// Get the chatbot's response based on user input
std::string VOTChatbot::getResponse(const std::string& userInput)
{
    std::string lowerCaseUserInput = upperToLowerCase(userInput);

    // Check if user's input have words that match the pre-defined greeting keywords 
    for (const auto& keywordMatching : greetingKeywords)
    {
        if(lowerCaseUserInput.find(keywordMatching.first) != std::string::npos)
        {
            // Extract username from user input. For now, assume it is always 'User' first
            std::string userName = "User"; // TODO: take the username from the database for this
            return fmt::format("{}, {}! How can I help you?", keywordMatching.second, userName);
        }
    }
    
    // Not matching any at all
    return "I'm sorry, I didn't understand that. Could you please try asking something else?";
}
