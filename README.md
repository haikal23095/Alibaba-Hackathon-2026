<div align="center">

# Rebound

### AI Agent for Flight Crisis Handling & Schedule Changes (Post-Booking)



[![Hackathon](https://img.shields.io/badge/Alibaba%20Cloud%20x%20Atlas-Agentic%20AI%20Hackathon-orange)]()
[![Platform](https://img.shields.io/badge/Built%20with-Qoder-blue)]()
[![Cloud](https://img.shields.io/badge/Powered%20by-Alibaba%20Cloud-FF6A00)]()
[![API](https://img.shields.io/badge/Data-Atlas%20Travel%20API-1f6feb)]()
[![License](https://img.shields.io/badge/License-MIT-green)]()

</div>

---

> 💡 **How to read this document**
> Every technical section opens with a plain-language explanation marked **🟢 For Everyone**,
> followed by the details marked **🔵 For Technical Readers**.
> That way this document is comfortable to read for non-technical people and developers alike.

---

## 📑 Table of Contents

**Concept**
- [Overview](#-overview)
- [A Simple Analogy](#-a-simple-analogy)
- [Problem Background](#-problem-background)
- [What Makes Rebound Different](#-what-makes-rebound-different)
- [Key Features](#-key-features)

**How It Works**
- [How the Agent Works](#️-how-the-agent-works)
- [Data Sources & the Role of the PNR](#-data-sources--the-role-of-the-pnr)
- [Authorization Model](#-authorization-model)
- [Agent Tools](#-agent-tools)
- [User Flows](#-user-flows)

**Technical**
- [Architecture](#-architecture)
- [Tech Stack](#-tech-stack)
- [Sandbox Environment](#-sandbox-environment)

**Impact & Judging**
- [Alignment with the UN SDGs](#-alignment-with-the-un-sdgs)
- [Judging Criteria Mapping](#️-judging-criteria-mapping)
- [Roadmap](#-roadmap)
- [Team](#-team)
- [License](#-license)

---

# 🧩 Concept

## 🧭 Overview

**Rebound** is an **agentic AI application** that solves the two most painful problems in air travel — the ones that have been most neglected:

1. **Flight disruptions** (delays / cancellations)
2. **Self-service schedule changes** with ticket policy validation

Unlike Online Travel Agents (OTAs) such as Traveloka or tiket.com, Rebound is **not** a place to buy new tickets. Rebound works in the **post-booking** phase: the user already holds a ticket, and Rebound's AI agent handles what happens *next* when plans change.

---

## 🍰 A Simple Analogy

Imagine you have already bought a ticket, and then something goes wrong (your flight is delayed) or you want to change your schedule.

- **The old way:** you have to call a call center, wait a long time, read confusing ticket rules, and find a solution on your own.
- **With Rebound:** you simply chat with an AI assistant — just like a normal conversation. The AI reads the rules, calculates the fees, searches for replacement flights, and handles the ticket reissue. **All you do is approve.**

> Rebound is like a **personal flight assistant** that is always on standby: it moves first when something goes wrong, and takes care of the complicated parts so you don't have to.

---

## 🎯 Problem Background

**🟢 For Everyone:** When a flight goes wrong, passengers are often confused and stressed. They have to handle everything themselves, even though the rules are complicated.

Post-booking problems that still aren't handled well:

- ⏳ Waiting for hours to reach a call center when a delay happens.
- 📜 **Fare rules** are complicated, opaque, and rarely explained in plain language.
- 😰 During a crisis, passengers are left to work out compensation and rebooking on their own.
- 🔁 Changing a schedule often involves confusing policies and hidden fees.

> OTAs are excellent at **selling** tickets, but offer little intelligent support **after** the purchase. **Rebound fills this gap.**

---

## 🆚 What Makes Rebound Different

Rebound deliberately strips out everything that doesn't serve its two core business processes:

| Traditional OTA (Traveloka / tiket.com) | Rebound (by design) |
| :--- | :--- |
| Search & buy new tickets | ❌ — the user already has a ticket |
| Hotel, train, and attraction catalogs | ❌ — focused on one problem |
| Many payment methods & checkout flows | ❌ — a single confirm button |
| Dashboards, tabs, complex navigation | ❌ — a single conversation screen |
| Users find solutions themselves | ✅ — **the AI acts proactively** |

> **Positioning:** *A Post-Booking Crisis & Change Handling Agent — not just another ticket booking app.*

---

## ✨ Key Features

- 🤖 **Proactive Crisis Handling** — the AI reacts to problems *before* the user asks for help.
- 📜 **Policy-Aware Reasoning** — the AI **always reads the fare rules first** before offering any change, then explains them in plain language.
- 💬 **Chat-Based Interface** — one conversation, no complicated menus.
- 🎨 **Dynamic UI Inside the Chat** — the AI renders flight cards, QR vouchers, and boarding passes directly in the chat, not just plain text.
- ⚡ **Minimal Interaction** — a crisis is resolved in ~2 taps; a schedule change in ~3 taps.
- 🔍 **Transparent & Auditable** — every AI decision comes with a clear policy rationale.

---

# ⚙️ How It Works

## ⚙️ How the Agent Works

**🟢 For Everyone:**
Rebound's AI works like a capable assistant. It **thinks → picks an action → performs it → checks the result → thinks again**, repeating until your problem is genuinely solved. It doesn't just guess an answer; it actually checks the data and the rules before acting.

**🔵 For Technical Readers:**
Rebound uses an **agentic loop (ReAct)** pattern. The LLM reasons, selects a *tool*, calls the Atlas API, observes the result, then reasons again until the goal is reached.

```
User / Signal  →  AI reasons  →  select an Action (Tool)  →  call the Atlas API
               →  observe the result  →  reason again  →  render result / ask  →  repeat
```

**Key rule — Policy-Aware Guardrail:**
The AI is **forbidden** from offering a replacement flight before validating the fare rules. This is what makes Rebound more than a mere "search bot" — it is a **decision-making agent** that genuinely understands policy.

---

## 🔑 Data Sources & the Role of the PNR

**🟢 For Everyone:**
An important question: *Is Rebound connected to Traveloka, where the ticket was bought?* **No.**

The actual ticket data is not stored at Traveloka — Traveloka is only the point of purchase (the storefront). The real data lives in the **airline's system**. What connects you to it is the **booking code / PNR** (a 6-character code such as `ABC123`) that you receive after buying the ticket.

> **Analogy:** a PNR is like a **bank account number**. Your money isn't kept inside the "number" — it's kept at the **bank**. The account number is just the *key* to access it. The same goes for a PNR: it's the key that unlocks your real ticket data.

**🔵 For Technical Readers:**
Rebound is **OTA-agnostic**. The authoritative booking data lives in the **airline system / GDS (Global Distribution System)** — such as Amadeus, Sabre, or Travelport. Rebound accesses it through the **Atlas Travel API**, using the **PNR** as the identifying key.

```
Passenger  →  Rebound (AI Agent)  →  Atlas Travel API  →  Airline System / GDS
                                                          (authoritative data source)
```

**A real example — Garuda on Nov 30 ➜ moving it up to Nov 26:**

```
Step 1  The passenger provides the PNR:  "Booking code ABC123, please move it up to Nov 26"
              │
Step 2  get_flight_status("ABC123")
        → Fetch the real data: "Garuda GA-xxx, Nov 30, class Y"
              │
Step 3  read_fare_rules("Y")           ← REQUIRED first (Policy-Aware)
        → "Class Y tickets are changeable, $50 admin fee"
              │
Step 4  search_alternatives("CGK", "SIN", "Nov 26", "Y")
        → Look for available seats on Nov 26
              │
Step 5  Render the Nov 26 flight card + fare difference + admin fee
              │
Step 6  The passenger taps "Confirm" → reissue_ticket() → the Nov 26 ticket is issued
```

> **The advantage:** because all it needs is a PNR, Rebound can serve tickets from **any OTA** without a separate integration per platform.

---

## 🔐 Authorization Model

**🟢 For Everyone:**
Rebound cannot change someone's ticket without permission. That is why Rebound operates on the basis of **user consent** — just like a financial app that may access your account only after you grant permission. The user grants authority, and Rebound then acts **on the user's behalf**.

**🔵 For Technical Readers:**
Rebound implements a *consent-first* **Trust & Authorization Layer**:

- **User consent** → the user authorizes Rebound to act on their behalf.
- **Official channels** → for OTA-managed tickets, integration happens through official partnerships/APIs.
- **Audit trail** → every agent action is logged and traceable.

> Aligned with one of the hackathon tracks: **"trust and verification systems"**.

---

## 🧰 Agent Tools

**🟢 For Everyone:** This is the list of "capabilities" the AI has — the concrete things it can actually do.

**🔵 For Technical Readers:** The agent's capabilities are exposed as a clearly defined *tool registry*:

| Tool | What it does | Used in |
| :--- | :--- | :--- |
| `get_flight_status(pnr)` | Fetches flight status & delay predictions | Flow 1 |
| `read_fare_rules(ticket_code)` | Reads the fare rules → changeable? at what cost? | Flows 1 & 2 |
| `search_alternatives(from, to, date, cabin_class)` | Searches for replacement flights | Flows 1 & 2 |
| `check_compensation(delay_minutes)` | Calculates compensation entitlements (voucher / snack) | Flow 1 |
| `hold_seat(flight_id)` | Temporarily locks a seat | Flows 1 & 2 |
| `reissue_ticket(pnr, new_flight)` | Reissues and updates the ticket | Flows 1 & 2 |

---

## 🔄 User Flows

Rebound has two main flows. Both are made as simple as possible.

### Flow 1 — Crisis Handling (the AI Moves First)

**🟢 In short:** the flight is delayed → the AI immediately notifies you and offers solutions (a voucher or a reschedule) → all you do is choose.

```
┌────────────────────────────────────────────────────────────────┐
│  1. The system detects that your flight is delayed              │
│     (simulated via a "Trigger" button during the demo)          │
└───────────────────────────┬────────────────────────────────────┘
                            ▼
┌────────────────────────────────────────────────────────────────┐
│  2. You receive a notification:                                 │
│     "Your flight is weather-affected. Tap to see options."      │
└───────────────────────────┬────────────────────────────────────┘
                            ▼
┌────────────────────────────────────────────────────────────────┐
│  3. The app opens → the AI greets you & explains the situation  │
└───────────────────────────┬────────────────────────────────────┘
                            ▼
                 ┌── AI assesses the delay length ──┐
                 ▼                                  ▼
      ┌─────────────────────┐        ┌──────────────────────────┐
      │ MINOR DELAY ≤ 2 hrs  │        │  MAJOR DELAY > 4 hrs      │
      │ → Voucher eligible   │        │  → Rescheduling advised   │
      └──────────┬──────────┘        └─────────────┬────────────┘
                 ▼                                  ▼
      ┌─────────────────────┐        ┌──────────────────────────┐
      │ Voucher card shown   │        │  2–3 replacement flights  │
      │ → "Claim" button     │        │  shown (Covered by the    │
      │                      │        │  Airline)                 │
      └──────────┬──────────┘        └─────────────┬────────────┘
                 ▼                                  ▼
      ┌─────────────────────┐        ┌──────────────────────────┐
      │ A QR code appears    │        │  You pick one → the AI    │
      │ (redeem at airport)  │        │  issues the new ticket →  │
      │ → DONE ✅             │        │  boarding pass → DONE ✅   │
      └─────────────────────┘        └──────────────────────────┘
```

### Flow 2 — Self-Service Schedule Change (the AI Reads the Rules for You)

**🟢 In short:** you type "change my schedule" → the AI reads the fare rules → it shows the options and costs transparently → you confirm → the new ticket is issued.

```
┌────────────────────────────────────────────────────────────────┐
│  1. You open the app & type your request in plain language:     │
│     "Change my flight from Singapore to tomorrow morning."      │
└───────────────────────────┬────────────────────────────────────┘
                            ▼
┌────────────────────────────────────────────────────────────────┐
│  2. The AI shows its process transparently:                     │
│     "Reading your ticket policy..."                             │
│     "Searching for available schedules..."                      │
└───────────────────────────┬────────────────────────────────────┘
                            ▼
┌────────────────────────────────────────────────────────────────┐
│  3. The AI reads the fare rules FIRST  ← the core selling point │
└───────────────────────────┬────────────────────────────────────┘
                            ▼
┌────────────────────────────────────────────────────────────────┐
│  4. The AI shows a flight card + the Policy Rationale:          │
│     "Class Y ticket — change permitted, $50 admin fee."         │
└───────────────────────────┬────────────────────────────────────┘
                            ▼
┌────────────────────────────────────────────────────────────────┐
│  5. You tap "Continue" → the AI shows the fare difference       │
└───────────────────────────┬────────────────────────────────────┘
                            ▼
┌────────────────────────────────────────────────────────────────┐
│  6. You tap "Confirm" → the new ticket is issued in the chat ✅  │
│     (no payment method to pick — just one button)               │
└────────────────────────────────────────────────────────────────┘
```

---

# 🏗 Technical

## 🏗 Architecture

**🟢 For Everyone:** Rebound's system is split into 4 "layers" that work together — from the interface you see, to the AI brain, to the bridge to the data, to the flight data source itself.

**🔵 For Technical Readers:** Four layers that map directly onto the hackathon's required technologies:

```
┌─────────────────────────────────────────────────────────────┐
│  1. PRESENTATION LAYER (Interface)                           │
│     Chat UI + dynamic component renderer (flight cards,      │
│     QR, boarding pass) + notification receiver               │
└───────────────────────────┬─────────────────────────────────┘
                            │  responses + rendering
┌───────────────────────────▼─────────────────────────────────┐
│  2. AGENT LAYER (AI Brain)   ── built on QODER ──            │
│     • LLM: Qwen (Alibaba Cloud Model Studio)                 │
│     • Orchestrator (ReAct reasoning loop)                    │
│     • Tool Registry (the agent's capability list)            │
│     • Policy-Aware Guardrail (fare rules must be validated)  │
└───────────────────────────┬─────────────────────────────────┘
                            │  tool calls
┌───────────────────────────▼─────────────────────────────────┐
│  3. INTEGRATION LAYER (Bridge)                               │
│     Alibaba Cloud Function Compute                           │
│     Tool wrappers → Atlas API + disruption signal receiver   │
└───────────────────────────┬─────────────────────────────────┘
                            │
┌───────────────────────────▼─────────────────────────────────┐
│  4. DATA & EXTERNAL SERVICES (Data Sources)                  │
│     • Atlas Travel API → bridge to Airline Systems / GDS     │
│     • Database (user PNRs, audit logs) — Alibaba RDS/Redis   │
└─────────────────────────────────────────────────────────────┘
```

**Technology mapping:**

| Layer | Technology | Role |
| :--- | :--- | :--- |
| AI Brain | **Qoder** | Builds & runs the core agentic reasoning |
| LLM & Compute | **Alibaba Cloud** | Qwen (Model Studio) + Function Compute + Database |
| Actions & Data | **Atlas Travel API** | Bridge to airline/GDS systems for all real operations |

---

## 🧪 Tech Stack

| Category | Technology |
| :--- | :--- |
| **Agent Platform** | Qoder |
| **LLM** | Qwen (Alibaba Cloud Model Studio) |
| **Compute** | Alibaba Cloud Function Compute |
| **Database** | Alibaba Cloud RDS / Redis |
| **Travel API** | Atlas Travel API |
| **Frontend** | Chat-based interface with dynamic components |

---

## 🧭 Sandbox Environment

**🟢 For Everyone:**
During the competition, Rebound does not touch real airline systems. Instead, the organizers provide a **sandbox environment** — a "practice room" filled with **simulated data** that is safe to test against.

It already contains **booking records (PNRs) seeded as the initial state**. When a user requests a schedule change, the agent **does not create a new booking** — it **updates the existing record**. Successfully changing that data is the **proof** that the agent works correctly.

**🔵 For Technical Readers:**
- Test data in the form of PNRs is *seeded* as the initial state in the Atlas API sandbox.
- The `reissue_ticket()` operation performs an *update* on an existing record, not a *create*.
- Comparing the before → after state (e.g. Nov 30 → Nov 26) serves as end-to-end functional validation.

> ℹ️ The disruption (delay) signal is also **simulated**, via a single trigger ("Trigger Storm") during the demo. All the logic behind it remains fully functional and representative of production conditions.

---

# 🌍 Impact & Judging

## 🌍 Alignment with the UN SDGs

| SDG | Relevance | Priority |
| :--- | :--- | :---: |
| **SDG 9 — Industry, Innovation & Infrastructure** | Builds modern agentic AI infrastructure that innovates the travel industry. | ⭐ Primary |
| **SDG 12 — Responsible Consumption & Production** | Efficient rescheduling reduces empty seats & wasted flights, lowering the carbon footprint. | Supporting |
| **SDG 8 — Decent Work & Economic Growth** | Automates repetitive support tasks, freeing human agents for complex cases. | Supporting |

---

## ⚖️ Judging Criteria Mapping

| Criterion | Weight | How Rebound Meets It |
| :--- | :---: | :--- |
| **Innovation** | 30% | Policy-Aware Reasoning, proactive AI, and dynamic UI inside the chat |
| **Feasibility** | 30% | A narrow post-booking scope, six well-defined tools, a concrete architecture, sandbox-ready |
| **Use of Qoder** | 20% | The agent's reasoning core is built & run on Qoder |
| **Impact & Presentation** | 20% | Real-world crisis storytelling + clear SDG alignment |

---

## 🗺 Roadmap

- [ ] Define the tool schemas (JSON) and register them in Qoder
- [ ] Implement the Integration Layer (Atlas API wrappers on Function Compute)
- [ ] Build the chat-based frontend with dynamic components
- [ ] Wire up the simulated disruption (delay) trigger
- [ ] Prepare the initial data (seed PNRs) in the sandbox
- [ ] End-to-end demo of both flows
- [ ] Pitch deck & presentation

---

## 👥 Team

> _Add your team members here._

| Name | Role |
| :--- | :--- |
| — | — |

---

## 📄 License

This project is released under the **MIT License**. See [`LICENSE`](LICENSE) for details.

---

<div align="center">

*Built for the Alibaba Cloud × Atlas Agentic AI Hackathon.*

**Rebound — when plans change, let the agent handle the rest.**

</div>
