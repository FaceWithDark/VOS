"use strict"; // Don't ask me why I need to do this, JS sucks in all shapes

document.querySelectorAll('input[name="roleCategory"]').forEach(roleCategoryButton => {
    roleCategoryButton.addEventListener(
        'change',
        function () {
            const appendLocation = document.querySelector(".admin-setting-page");

            if (this.id === "general") {
            appendLocation.innerHTML = `
            <div class="admin-add-role-panel">
                <h2>Add User for General Role Category</h2>

                <form action="/setting/admin" target="_self" method="post">
                    <label for="userId">ID</label>
                    <input type="text" name="userId" placeholder="E.G: 2" minlength="1" maxlength="10" autofocus required />
                    <br />

                    <label for="userRole">Role</label>
                    <input type="text" name="userRole" placeholder="E.G: USR" minlength="3" maxlength="5" autofocus required />
                    <br />

                    <input type="submit" value="Add General Role">
                </form>
            </div>

            <div class="admin-view-data-panel">
                <h2>View User Data</h2>
                <p>COMING SOON!!</p>
            </div>
            `;
            } else if (this.id === 'tournament') {
                appendLocation.innerHTML = `
                <div class="admin-add-role-panel">
                    <h2>Add User for Tournament Role Category</h2>

                    <form action="/setting/admin" target="_self" method="post">
                        <label for="userId">ID</label>
                        <input type="text" name="userId" placeholder="E.G: 2" minlength="1" maxlength="10" autofocus required />
                        <br />

                        <label for="userRole">Role</label>
                        <input type="text" name="userRole" placeholder="E.G: USR" minlength="3" maxlength="5" autofocus required />
                        <br />

                        <label for="userTournament">Tournament</label>
                        <input type="text" name="userTournament" placeholder="E.G: VOT5" minlength="4" maxlength="5" autofocus required />
                        <br />

                        <input type="submit" value="Add Tournament Role">
                    </form>
                </div>

                <div class="admin-view-data-panel">
                    <h2>View User Data</h2>
                    <p>COMING SOON!!</p>
                </div>
                `;
            } else {
                // Just to annoy those abusing the F12 trick
                console.error("What are you tryin' to do, huh?");
                setTimeout(
                    () => { window.location.reload(); },
                    1000
                );
            }
        }
    );
});
