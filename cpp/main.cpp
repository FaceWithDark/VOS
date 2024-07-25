#include "chatbot.hpp"
#include <iostream>

int main()
{
    // Create an instance of the 'VOT Chatbot' class
    VOTChatbot votchatbot;

    /* Sample user input:
        std::string userInput = "Hi, VOT Copilot!";
        std::string userInput = "How do I get to the home page?";
        std::string userInput = "what does dark / light mode do?";
        std::string userInput = "I am having issue with my account.";
        std::string userInput = "Goodbye, VOT Copilot!";   
    */

    // Store user input as string
    std::string userInput;

    // Prompt user to type in for further use
    std::cout << "Query: ";

    // Get user input and sent to chatbot to analyse it
    std::getline(std::cin, userInput);

    // Get the chatbot's response
    std::string chatbotResponse = votchatbot.getResponse(userInput);

    // Output the response
    std::cout << chatbotResponse << std::endl;
    
    return 0;
}
