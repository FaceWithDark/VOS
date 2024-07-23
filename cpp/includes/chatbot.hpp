#if !defined(CHATBOT_HPP)
#define CHATBOT_HPP

#include <string>
#include <unordered_map>
#include <vector>

class VOTChatbot {
public:

    VOTChatbot();
    // Output the defined queries that has been prepared to get display out to the terminal
    std::string respondQuery(const std::string& query);

private:
    // Process the defined queries to get ready to be ouput to the terminal 
    std::string processQuery(const std::string& query);

    // Data member for storing predefined responses
    std::unordered_map<std::string, std::string> predefinedResponses;

    std::vector<std::string> greetingQuery;     // Query for greeting keywords
    std::vector<std::string> directionQuery;    // Query for direction keywords

    // Check if the queries match any string from the string provided into queries vectors
    bool isQueryMatch(const std::vector<std::string>& queries, const std::string& query);
};

#endif
