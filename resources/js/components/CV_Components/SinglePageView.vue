<template>
  <div>
    <template v-if="cvData.education && educationEnabledInfo">
      <div class="row mx-0 mb-3 section-row">
        <div class="col-12 p-1 section-title">
          <h4 class="section-header">{{ $t('education') }}</h4>
        </div>
        <div class="col-12 px-1 mt-2 interests-section">
          <div v-for="education in cvData.education" :key="education.id">
            <div class="col-12 px-0">
              <span class="education-date">{{ education.education_dates }}</span> {{ $t(education.education_name) }}
            </div>
          </div>
        </div>
      </div>
    </template>
    <template v-if="cvData.experience && experienceEnabledInfo">
      <div class="row mx-0 section-row">
        <div class="col-12 section-title p-1 mb-2">
          <h4 class="section-header">{{ $t('experience') }}</h4>
        </div>
        <div class="col-12 mt-2 px-2 experience-section">
          <div v-for="(exp, i) in cvData.experience" :key="exp.id">
            <div class="row mx-0 mb-4">
              <div class="col-sm-3 px-0 job-label">
                <div class="exp-title col-12 px-0">{{ $t(exp.job_title) }}</div>
                <div class="exp-place col-12 px-0">{{ exp.job_place }}</div>
                <small class="exp-city col-12 px-0">{{ exp.job_city }}</small>
                <div class="exp-dates col-12 px-0">{{ exp.job_start }} - {{ exp.job_end }}</div>
              </div>
              <template v-if="exp.job_desc">
                <div class="col-sm-9 px-0">
                  <div class="exp-desc col-12" v-for="desc in exp.job_desc">
                    - {{ $t(desc )}}
                  </div>
                </div>
              </template>
            </div>
            <hr v-if="i < cvData.experience.length - 1 && cvData.experience.length > 1">
          </div>
        </div>
      </div>
    </template>
    <template v-if="cvData.skills && skillsEnabledInfo">
      <div class="row mx-0 mb-3 section-row">
        <div class="row mx-0 col-12 p-1 section-title">
          <h4 class="section-header">{{ $t('skills') }}<b-icon v-b-popover.hover.top="'Hover over skill names to show the proficiency'" icon="info-circle" class="info-icon ml-1"></b-icon></h4>
        </div>
        <div class="col-12 px-0 mt-2 skills-section">
          <div v-for="(skill, i) in cvData.skills" :key="skill.id">
            <template class="col-12" v-if="skill.show_progress">
              <span class="font-weight-bold" v-b-popover.hover.top="'(' + $t(skill.skill_level) + ')'">{{ skill.skill_name }}</span>
              <b-progress height="15px" class="skill-progress mb-2" variant="info" :value="skill.skill_value" show-progress></b-progress>
            </template>
          </div>
          <div class="row mx-0">
            <div v-for="skill in cvData.skills" :key="skill.id">
              <template class="d-inline-block" v-if="!skill.show_progress">
                <div class="skill-item px-0 mr-2" v-b-popover.hover.top="'(' + $t(skill.skill_level) + ')'">{{ skill.skill_name }}</div>
              </template>
            </div>
          </div>
        </div>
      </div>
    </template>
    <template v-if="cvData.interests && interestsEnabledInfo">
      <div class="row mx-0 mb-3 section-row">
        <div class="col-12 p-1 section-title">
          <h4 class="section-header">{{ $t('interests') }}</h4>
        </div>
        <div class="col-12 px-2 mt-2 interests-section">
          <div v-for="interest in cvData.interests" :key="interest.id">
            <div class="col-12"><li>{{ $t(interest.interest_name) }}</li></div>
          </div>
        </div>
      </div>
    </template>
    <template v-if="cvData.other_projects && otherEnabledInfo">
      <div class="row mx-0 mb-3 section-row">
        <div class="col-12 p-1 section-title">
          <h4 class="section-header">{{ $t('otherProjects') }}</h4>
        </div>
        <div class="col-12 px-0 mt-2 other-section">
          <div v-for="other in cvData.other_projects">
            <b-card class="px-0 mb-2 project-card">
              <div class="row mx-0">
                <div class="col-6 px-0">
                  <h4 class="col-12 px-0 mb-0">{{ $t(other.project_name) }}</h4>
                  <small class="col-12 px-0 font-weight-bold">{{ $t(other.project_desc) }}</small>
                </div>
                <template v-if="other.project_details">
                  <div class="col-6">
                    <h5>{{ $t('projectDetails') }}</h5>
                    <div v-for="desc in other.project_details">
                      <span class="project-tech">
                        {{ $t(desc) }}
                      </span>
                    </div>
                  </div>
                </template>
              </div>
              <div class="col-12 px-0 text-left" v-if="other.project_link">
                <a :href="other.project_link" target="_blank">
                  <small>{{ $t(other.project_name) }}</small>
                </a>
              </div>
            </b-card>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script>
export default {
  name: 'SinglePageView',
  props: ['singleData', 'educationEnabledInfo', 'experienceEnabledInfo', 'skillsEnabledInfo', 'interestsEnabledInfo', 'otherEnabledInfo'],
  data() {
    return {
      cvData: this.singleData,
      educationEnabled: this.educationEnabledInfo,
      experienceEnabled: this.experienceEnabledInfo,
      skillsEnabled: this.skillsEnabledInfo,
      interestsEnabled: this.interestsEnabledInfo,
      otherEnabled: this.otherEnabledInfo
    }
  }
}
</script>

<style scoped>
  .info-icon {
    width: 15px;
    height: 15px;
    top: 10px;
    position: absolute;
    font-weight: 600;
  }
  .section-title {
    background: linear-gradient(90deg, rgba(232,241,242,1) 0%, rgba(255,255,255,1) 100%);
  }
  .section-row:hover .section-title {
    background: linear-gradient(90deg, rgb(181, 227, 232) 0%, rgb(255, 255, 255) 100%)
  }
  .section-row:hover .section-title .section-header{
    color: #325B5B !important;
    font-weight: 600 !important;
  }
  .section-header {
    margin: 0px;
    padding: 2px;
  }
  .education-date {
    font-weight: 600;
    margin-right: 5px;
  }
  .exp-title {
    font-weight: 600;
    color: #3D7575;
  }
  .exp-dates {
    color: #2E7373;
  }
  .job-label {
    border-right: 1px solid rgba(0, 0, 0, 0.1);
  }
  .skill-item:hover {
    font-weight: 600;
    cursor: pointer;
  }
  .skill-item:before {
    content: "\A";
    width: 10px;
    height: 10px;
    margin-right: 4px;
    border-radius:50%;
    background: #2E7373;
    display: inline-block;
  }
  .project-tech {
    font-size: 12px;
  }
  .project-card:hover {
    background-color: #F3FDFF;
  }
</style>