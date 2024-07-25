#include "chatbot.hpp"
#include <algorithm>
#include <fmt/core.h>

// Constructor to initialise the chatbot
VOTChatbot::VOTChatbot()
{
    // Initialise greeting keywords and responses
    greetingKeywords = {
        {"hi", "Hi"},
        {"hello", "Hello"},
        {"hey", "Hey"}
    };

    // Initialise navigation keywords and responses
    navigationKeywords = {
        {"how", "To get to"},
        {"where", "You can find"}
    };

    // Initialise explanation keywords and responses
    explanationKeywords = {
        {"what", "allows you to"},
        {"how", "helps you to"}
    };

    // Initialise troubleshoot keywords and responses
    troubleshootKeywords = {
        {"trouble", "To troubleshoot this issue, you can"},
        {"issue", "To resolve this issue, you should"},
        {"problem", "To fix this problem. you need to"},
        {"hardtime", "To overcome this difficulty, you can"}
    };

    // Initialise goodbye keywords and responses
    goodbyeKeywords = {
        {"exit", "You blyat"},
        {"goodbye", "Goodbye"},
        {"see you", "See you"},
        {"see ya", "See ya"},
        {"see yah", "See yah"},
        {"see you later", "See you later"}
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
    
    // Check if user's input have words that match the pre-defined navigation keywords
    for (const auto& keywordMatching : navigationKeywords)
    {
        if(lowerCaseUserInput.find(keywordMatching.first) != std::string::npos)
        {
            // Extract page name from user input. For now, assume it is always 'Home Page' first
            std::string pageName = "home page"; // TODO: set an array of pages for this or maybe database method
            return fmt::format("{} the {}, you will have to follow these steps: [instructions].", keywordMatching.second, pageName);
        }
    }
    
    // Check if user's input have words that match the pre-defined explanation keywords
    for (const auto& keywordMatching : explanationKeywords)
    {
        if(lowerCaseUserInput.find(keywordMatching.first) != std::string::npos)
        {
            // Extract feature name from user input. For now, assume it is always 'Dark / Light Mode' first
            std::string featureName = "dark / light mode"; // TODO: set an array of pages for this or maybe database method
            return fmt::format(" {} {}: [explains to feature].", featureName, keywordMatching.second);
        }
    }
    
    // Check if user's input have words that match the pre-defined troubleshoot keywords
    for (const auto& keywordMatching : troubleshootKeywords)
    {
        if(lowerCaseUserInput.find(keywordMatching.first) != std::string::npos)
        {
            // Extract issue description from user input. For now, make it as a general comment first
            std::string issueName = "[detailed description on how to troubleshoot]"; // TODO: set an array of pages for this or maybe database method
            return fmt::format(" {}: {}.", keywordMatching.second, issueName);
        }
    }
    
    // Check if user's input have words that match the pre-defined goodbye keywords
    for (const auto& keywordMatching : goodbyeKeywords)
    {
        if(lowerCaseUserInput.find(keywordMatching.first) != std::string::npos)
        {
            // Extract username from user input. For now, assume it is always 'User' first
            std::string userGoodbyeName = "User"; // TODO: take the username from the database for this
            return fmt::format(" {}, {}!", keywordMatching.second, userGoodbyeName);
        }
    }
    
    // Not matching any at all
    return "I'm sorry, I didn't understand that. Could you please try asking something else?";
}
