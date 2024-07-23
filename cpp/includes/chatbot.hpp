#if !defined(CHATBOT_HPP)
#define CHATBOT_HPP

#include <string>

class VOTChatbot {
public:
    VOTChatbot();
    std::string respond(const std::string& query);
private:
    std::string processQuery(const std::string& query);
};

#endif
