#include "chatbot.hpp"
#include <iostream>

int main()
{
    // Create an instance of the 'VOT Chatbot' class
    VOTChatbot votchatbot;

    /* Sample user input:
        std::string userInput = "Hi, VOT Copilot!";
    */
    std::string userInput = "How do I get to the home page?";
    
    // Get the chatbot's response
    std::string chatbotResponse = votchatbot.getResponse(userInput);

    // Output the response
    std::cout << chatbotResponse << std::endl;
    
    return 0;
}
