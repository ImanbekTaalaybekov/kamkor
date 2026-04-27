import { useState } from "react";
import type { SessionData } from "../../auth/auth.types";
import BottomNav, { type TabKey } from "../../components/BottomNav";
import DashboardPage from "./DashboardPage";
import ContactsPage from "./ContactsPage";
import TemplatePage from "./TemplatePage";
import InstructionsPage from "./InstructionsPage";
import SosHistoryPage from "./SosHistoryPage";
import PsychologicalHelpPage from "./PsychologicalHelpPage";
import CentersPage from "./CentersPage";
import ProfilePage from "./ProfilePage";

type Props = {
  session: SessionData | null;
  onLoggedOut: () => void;
};

type InnerScreen = "tabs" | "instructions" | "history" | "centers";

export default function MainMenuPage({ session, onLoggedOut }: Props) {
  const [activeTab, setActiveTab] = useState<TabKey>("home");
  const [innerScreen, setInnerScreen] = useState<InnerScreen>("tabs");

  const handleTabChange = (tab: TabKey) => {
    setActiveTab(tab);
    setInnerScreen("tabs");
  };

  if (innerScreen === "instructions") {
    return (
      <div className="mobile-shell">
        <div className="mobile-screen">
          <InstructionsPage onBack={() => setInnerScreen("tabs")} />
          <BottomNav activeTab={activeTab} onChange={handleTabChange} />
        </div>
      </div>
    );
  }

  if (innerScreen === "history") {
    return (
      <div className="mobile-shell">
        <div className="mobile-screen">
          <SosHistoryPage onBack={() => setInnerScreen("tabs")} />
          <BottomNav activeTab={activeTab} onChange={handleTabChange} />
        </div>
      </div>
    );
  }

  if (innerScreen === "centers") {
    return (
      <div className="mobile-shell">
        <div className="mobile-screen">
          <CentersPage onBack={() => setInnerScreen("tabs")} />
          <BottomNav activeTab={activeTab} onChange={handleTabChange} />
        </div>
      </div>
    );
  }

  return (
    <div className="mobile-shell">
      <div className="mobile-screen">
        {activeTab === "home" ? (
          <DashboardPage
            session={session}
            onOpenInstructions={() => setInnerScreen("instructions")}
            onOpenHistory={() => setInnerScreen("history")}
            onOpenPsychologicalHelp={() => setActiveTab("psych")}
            onOpenCenters={() => setInnerScreen("centers")}
          />
        ) : null}
        {activeTab === "psych" ? <PsychologicalHelpPage onBack={() => setActiveTab("home")} /> : null}
        {activeTab === "contacts" ? <ContactsPage session={session} onBack={() => setActiveTab("home")} /> : null}
        {activeTab === "template" ? <TemplatePage onBack={() => setActiveTab("home")} /> : null}
        {activeTab === "profile" ? <ProfilePage session={session} onBack={() => setActiveTab("home")} onLoggedOut={onLoggedOut} /> : null}
        <BottomNav activeTab={activeTab} onChange={handleTabChange} />
      </div>
    </div>
  );
}
