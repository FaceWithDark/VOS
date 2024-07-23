#include "chatbot.hpp"
#include <iostream>

int main() {
    VOTChatbot vot_chatbot;
    std::string query;

    while(true)
    {
        std::cout << "Enter your query: ";
        std::getline(std::cin, query);

        if(query == "exit")
        {
            std::cout << "Goodbye!\n";
            break;
        }

        std::string queryResponse = vot_chatbot.respondQuery(query);
        std::cout << queryResponse << std::endl;
    }

    return 0;
}
