# VOS Git Commit Convention Guideline
This project strictly follows [**Convential Commit**](https://www.conventionalcommits.org/en/v1.0.0/) style for each Git commit pushed with some custom tags defined by us so that it matches our workflow. These including:

1. `init`: for commits that set a new default setup to the project. This could be: repo structure, **Docker Compose** setup, etc.
2. `chore`: for commits that doesn't fit with all other tags. This one would be the one to be used a lot if you unsure what tags to use.
3. `feat`: for commits that propose a new feature, implmentation, etc
4. `doc`: for commits that update/add documentation files. This's mostly used for file under `/docs` directory or those `.md` file at project root directory.
5. `fix`: for commits that resolve submitted bugs/issues from [**GitHub Issues**](https://github.com/FaceWithDark/VOS/issues) page.
6. `revert`: for commits that pushed back changes that shouldn't be allowed/approved to one of the core branches. This can be used to revert changes in `main` branch if latest PR to `main` 'cause serious problems on production evironment.
7. `docker`: for commits that update files related to **Docker** setup, or add/modify new/current `Dockerfile` file if needed as the complexity of the project goes on.
8. `review`: for commits that resolve reviews received during any created PR. Please use this with a very minimal info provided right next to the tag so that we can differenciate which sort of review is/are this when backtracking (e.g., `review(new-service)`).
