#if !defined(VOTCHATBOT_HPP)
#define VOTCHATBOT_HPP

#include <string>
#include <unordered_map>

class VOTChatbot {
public:
    // Constructor to initialise the chatbot
    VOTChatbot();

    // Get the chatbot's response based on user input
    std::string getResponse(const std::string& userInput);

private:
    // Convert any uppercase string to lowercase string to support both typing format
    std::string upperToLowerCase(const std::string& keywordString);

    // Pre-defined keywords and responses
    std::unordered_map<std::string, std::string> greetingKeywords;
};

#endif
