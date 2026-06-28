# Contributing guidelines

Thank you for showing interest in the development of VOS project! To maintain structure and ensure high-quality tracking of bugs and features, we utilise structured templates for all **Issues** and **PRs**. Please adhere to the following minimal guidelines when contributing.


## Submitting Issues

1. Navigate to the **Issues** tab of the repository.
2. Click on **New Issue**.
3. You will be prompted with a choice of templates. Select the **BUG REPORT** template *(or the most appropriate template for your issue type)* to pre-populate the description fields.


## Submitting PRs

When submitting a PR, you must explicitly inject the query parameters into the URL to load the correct PR template from the `.github/PULL_REQUEST_TEMPLATE` directory. An example PR flow would be:

1. Push your changes and click on the **Create a pull request** button on GitHub.
2. Modify the generated URL by appending the quick pull and `template` parameters:

```md
- Example PR URL:
`https://github.com/<your account>/<forked repo>/compare/<created branch>`

- Inject query params:
`?quick_pull=1&template=<PR template>.md` *(Choose the suitable one from the `.github/PULL_REQUEST_TEMPLATE` directory)*

- Final injected URL Result:
`https://github.com/<your account>/<forked repo>/compare/<created branch>?quick_pull=1&template=<PR template>.md`
```

3. Paste the final URL onto the search bar, verify the template populated correctly, and submit the PR.
