#include "chatbot.hpp"
#include <iostream>

int main() {
    VOTChatbot vot_chatbot;
    std::string query;

    std::cout << "Enter your query: ";
    std::getline(std::cin, query);

    std::string queryResponse = vot_chatbot.respondQuery(query);
    std::cout << queryResponse << std::endl;

    return 0;
}
