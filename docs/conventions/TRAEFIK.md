# VOS Domain Specificity Convention

To prevent catch-all wildcard routing rules from accidentally hijacking traffic meant for explicit microservices or administrative panels, our project enforces a strict pattern for defining [**Traefik router priorities**](https://doc.traefik.io/traefik/reference/routing-configuration/http/routing/rules-and-priority/#priority-calculation).

This pattern mirrors the concept of [**CSS Specificity**](https://developer.mozilla.org/en-US/docs/Web/CSS/Guides/Cascade/Specificity):
> _"The more granular and explicit the route, the higher its priority score."_

---
# VOS Domain Specificity Scale

Because our dynamic tenant regex rules are bundled alongside their parent domains (e.g., our Symfony container's Traefik rule at `proxy/dynamic/app.yaml`), they inherit the tier of that parent. We categorise priorities into 100-point buckets based strictly on structural location:


| Priority range | Tier list | Type definition | Matching logic | Example rule |
| :---: | :---: | :--- | :--- | :--- |
| **600 – 699** | **Tier 6** | **Sub of subdomain + Path** | Explicit deep subdomain with a path modifier | ```Host(`dev.vot.vososu.site`) && PathPrefix(`/api`)``` |
| **500 – 599** | **Tier 5** | **Sub of subdomain only** | Explicit deep subdomain root | ```Host(`dev.vot.vososu.site`)``` |
| **400 – 499** | **Tier 4** | **Subdomain + Path** | Standard subdomain with a path modifier | ```Host(`vot.vososu.site`) && PathPrefix(`/api`)``` |
| **300 – 399** | **Tier 3** | **Subdomain only** | Standard explicit subdomain root | ```Host(`vot.vososu.site`)``` |
| **200 – 299** | **Tier 2** | **Base domain + Path** | Apex domain with a path modifier | ```Host(`vososu.site`) && PathPrefix(`/api`)``` |
| **100 – 199** | **Tier 1** | **Base domain / Multi-Tenant root** | Root apex or domain-paired tenant wildcards | ```Host(`vososu.site`)``` |
| **1 – 99** | **Tier 0** | **Special / Infrastructure** | Global catch-alls, maintenance, and error pages | ```PathPrefix(`/`)``` |

---
# VOS Domain Specificity Guideline

1. **Path dominance**

Within the same domain level, a rule containing a `Path` or `PathPrefix` modifier **MUST** be placed in the upper half of its bucket (`150 - 199`) relative to its host-only counterpart (`100 - 149`).

2. **The special tier (Tier 0)**

This tier is explicitly isolated for global infrastructure utilities. Do not put application features or active services here. It is strictly reserved for routers that **capture unmatched traffic to serve global 404s, security rate-limit walls, or static** `Maintenance Mode` **assets**.

3. **Leave breathing room**

Assign priorities in increments of **5 - 10** (e.g., `310`, `320`, `330`). This allows future developers to insert intermediate routing layers or custom middleware overrides without refactoring the entire tier.

4. **The zero rule (`0`)**

Never explicitly assign a priority of `0`. Traefik interprets `0` as an instruction to fall back to its default string-length calculation, which breaks our isolation design.

5. **No negative bounds**

Negative integers are banned across all environments to keep infrastructure metrics and debugging simple.

---
# VOS Domain Specificity Example

Consider how Traefik evaluates an incoming request for your administrative database tool at `http://db.vot.vososu.site` versus your core backend application:

1. **The Multi-Tenant Router** matches via its combined base/regex rule in `Tier 2`, carrying a baseline priority of `150`.

2. **The pgAdmin Router** explicitly claims the host string in `Tier 5`, carrying an explicit priority of `550`.

Because `550 > 150`, Traefik bypasses the broad multi-tenant regex engine for this request slot and allows developers route safely into the **multi-tenant's administrative database tool**.
